import { api } from '../api.js';
import { confirmDialog, el, emptyBlock, errorBlock, loadingBlock, pageData, showAlert, toast, withBusy } from '../ui.js';

const STATUS = { planned: 'bg-blue-50 text-blue-800', taught: 'bg-emerald-50 text-emerald-800', off: 'bg-amber-50 text-amber-900', makeup: 'bg-violet-50 text-violet-800', cancelled: 'bg-red-50 text-red-800' };

export default function adminMakeup(root) {
    const page = pageData(); const region = (name) => root.querySelector(`[data-region="${name}"]`);
    const termSelect = root.querySelector('[data-filter="term"]'); const sectionSelect = root.querySelector('[data-filter="section"]'); const form = root.querySelector('[data-form="makeup"]');
    const roomSelect = root.querySelector('[data-room-filter]'); const roomDate = root.querySelector('[data-room-date]');
    const state = { terms: [], sections: [], rooms: [], periods: [], madeUpFor: new Set() };
    const url = (template, id) => template.replace('__ID__', encodeURIComponent(id));

    termSelect.addEventListener('change', loadSections); sectionSelect.addEventListener('change', loadSessions);
    root.querySelector('[data-action="reload"]').addEventListener('click', loadSessions); root.querySelector('[data-action="generate"]')?.addEventListener('click', generate);
    root.querySelector('[data-action="room-schedule"]').addEventListener('click', loadRoomSchedule); form?.addEventListener('submit', makeup);
    bootstrap();

    async function bootstrap() {
        region('sessions').replaceChildren(loadingBlock(3));
        try {
            const [years, rooms, periods] = await Promise.all([api.get(page.api.terms), api.get(page.api.rooms), api.get(page.api.periods)]);
            state.terms=(years?.data??[]).flatMap((y)=>(y.terms??[]).map((t)=>({...t,academic_year:y.name}))); state.rooms=rooms?.data??[]; state.periods=periods?.data??[];
            fill(termSelect,state.terms,(x)=>`${x.name} · ${x.academic_year}`); fill(roomSelect,state.rooms,(x)=>`${x.code} · ${x.capacity} chỗ`); fill(form?.elements.room_id,state.rooms,(x)=>`${x.code} · ${x.capacity} chỗ`,true,'Không chọn'); fillPeriods();
            const now = new Date(); roomDate.value = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`; await loadSections();
        } catch(error){region('sessions').replaceChildren(errorBlock(error,bootstrap));}
    }

    async function loadSections(){
        try{const data=await api.get(page.api.sections,{term_id:termSelect.value}); state.sections=data?.data??[]; fill(sectionSelect,state.sections,(x)=>`${x.code} — ${x.subject?.code??''} ${x.subject?.name??''}`); await loadSessions();}
        catch(error){showAlert(region('alert'),error);}
    }

    async function loadSessions(){
        const id=sectionSelect.value; if(!id){region('sessions').replaceChildren(emptyBlock('Không có lớp học phần'));return;} region('sessions').replaceChildren(loadingBlock(4));
        try{const data=await api.get(url(page.api.sessions,id)); root.querySelector('[data-field="session-summary"]').textContent=`${data.total} buổi · ${data.counted} buổi tính chuyên cần · ${data.needs_makeup} buổi cần dạy bù`; const rows=data.data??[]; state.madeUpFor = new Set(rows.filter((x) => x.makeup_for_id).map((x) => Number(x.makeup_for_id))); region('sessions').replaceChildren(rows.length?el('div',{class:'space-y-2'},rows.map(sessionCard)):emptyBlock('Chưa có buổi học','Dùng “Sinh lại buổi học” sau khi lớp đã có mẫu lịch.','event_busy'));}
        catch(error){region('sessions').replaceChildren(errorBlock(error,loadSessions));}
    }

    function sessionCard(s){
        const actions=[]; const needs=['off','cancelled'].includes(s.status) && !state.madeUpFor.has(Number(s.id));
        if(page.can.create&&needs) actions.push(el('button',{type:'button',class:'text-xs text-primary font-semibold hover:underline',onclick:()=>selectMakeup(s)},'Dạy bù'));
        if(page.can.update&&s.status==='planned') actions.push(el('button',{type:'button',class:'text-xs text-red-700 font-semibold hover:underline',onclick:(e)=>cancel(e.currentTarget,s)},'Báo nghỉ / Hủy'));
        return el('article',{class:'rounded-xl border border-outline-variant p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3'},el('div',{},el('p',{class:'font-semibold'},`${s.date} · Tuần ${s.week} · Tiết ${s.period_from}–${s.period_to}`),el('p',{class:'text-xs text-on-surface-variant'},`Phòng ${s.room??'—'}${s.off_reason?` · ${s.off_reason}`:''}${s.note?` · ${s.note}`:''}`)),el('div',{class:'flex items-center gap-3'},el('span',{class:`rounded-full px-2 py-1 text-[11px] font-semibold ${STATUS[s.status]??'bg-slate-100'}`},s.status_label),actions));
    }

    function selectMakeup(s){if(!form)return; form.elements.session_id.value=s.id; root.querySelector('[data-field="source-session"]').value=`#${s.id} · ${s.date} · tiết ${s.period_from}-${s.period_to} · ${s.status_label}`; form.querySelector('button[type="submit"]').disabled=false; form.scrollIntoView({behavior:'smooth',block:'center'});}

    async function makeup(event){event.preventDefault(); const id=form.elements.session_id.value;if(!id)return; const button=form.querySelector('button[type="submit"]'); const body={date:form.elements.date.value,period_from:Number(form.elements.period_from.value),period_to:Number(form.elements.period_to.value),note:form.elements.note.value.trim()}; if(form.elements.room_id.value)body.room_id=Number(form.elements.room_id.value);
        try{await withBusy(button,()=>api.post(url(page.api.makeup,id),body));toast('Đã xếp buổi dạy bù.','success');form.reset();form.elements.session_id.value='';root.querySelector('[data-field="source-session"]').value='';button.disabled=true;fill(form.elements.room_id,state.rooms,(x)=>`${x.code} · ${x.capacity} chỗ`,true,'Không chọn');fillPeriods();await loadSessions();}catch(error){showAlert(region('alert'),error);}}

    async function cancel(button,s){const ok=await confirmDialog({title:`Hủy/báo nghỉ buổi ${s.date}?`,danger:true,input:{label:'Lý do',required:true,maxlength:255},confirmText:'Xác nhận'});if(!ok)return;try{await withBusy(button,()=>api.post(url(page.api.cancel,s.id),{reason:ok.value}));toast('Đã cập nhật buổi học.','success');await loadSessions();}catch(error){showAlert(region('alert'),error);}}

    async function generate(event){const button=event.currentTarget;const id=sectionSelect.value;if(!id)return;const ok=await confirmDialog({title:'Sinh lại các buổi học?',message:'Buổi tương lai ở trạng thái Dự kiến/Nghỉ được đồng bộ lại từ mẫu lịch; lịch sử buổi đã dạy/dạy bù/hủy được giữ.',confirmText:'Sinh buổi'});if(!ok)return;try{await withBusy(button,()=>api.post(url(page.api.generate,id),{}));toast('Đã sinh các buổi học.','success');await loadSessions();}catch(error){showAlert(region('alert'),error);}}

    async function loadRoomSchedule(){const roomId=roomSelect.value;if(!roomId)return;region('room-schedule').replaceChildren(loadingBlock(2));try{const data=await api.get(page.api.timetable,{target:'room',id:roomId,term_id:termSelect.value,date:roomDate.value,view:'week'});const rows=data.sessions??[];region('room-schedule').replaceChildren(rows.length?el('div',{class:'space-y-2'},rows.map((s)=>el('div',{class:'rounded-lg border border-outline-variant p-2 text-xs'},`${s.date??''} · ${s.time??`Tiết ${s.period_from??''}-${s.period_to??''}`} · ${s.section_code??s.course_section?.code??s.section?.code??'Lớp học'}`))):emptyBlock('Phòng không có buổi học trong tuần đang xem','Lưu ý API availability hiện chỉ kiểm tra bảo trì, chưa phải công cụ gợi ý phòng trống theo tiết.','meeting_room'));}catch(error){region('room-schedule').replaceChildren(errorBlock(error,loadRoomSchedule));}}

    function fill(select,items,label,blank=false,blankText='—'){if(!select)return;select.replaceChildren();if(blank)select.append(el('option',{value:''},blankText));items.forEach((x)=>select.append(el('option',{value:x.id},label(x))));}
    function fillPeriods(){for(const select of [form?.elements.period_from,form?.elements.period_to]){if(!select)continue;select.replaceChildren();state.periods.forEach((p)=>select.append(el('option',{value:p.number},`Tiết ${p.number} · ${p.starts_at}–${p.ends_at}`)));}}
}

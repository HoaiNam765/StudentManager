<?php

namespace App\Support\Exceptions;

use RuntimeException;

/**
 * Lỗi do vi phạm quy tắc nghiệp vụ (BR-…), không phải lỗi hệ thống.
 * Thông báo bằng tiếng Việt và nên nêu cách khắc phục (GC-04, UX-09).
 *
 * Ví dụ: throw new BusinessRuleException(
 *     'Thiếu học phần tiên quyết: Giải tích 1.',
 *     'Hãy đăng ký và hoàn thành học phần này trước.'
 * );
 */
class BusinessRuleException extends RuntimeException
{
    public function __construct(string $message, protected ?string $hint = null)
    {
        parent::__construct($message);
    }

    public function hint(): ?string
    {
        return $this->hint;
    }

    /** Nội dung hiển thị cho người dùng: lý do kèm cách khắc phục. */
    public function userMessage(): string
    {
        return $this->hint === null || $this->hint === ''
            ? $this->getMessage()
            : $this->getMessage().' '.$this->hint;
    }
}

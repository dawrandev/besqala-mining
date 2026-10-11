<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Applicant = 'applicant';
    case Direksiya = 'direksiya';
    case Expert = 'expert';
    case Authority = 'authority';
    case Admin = 'admin';

    /**
     * Human-readable label shown in the UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Applicant => __('roles.applicant'),
            self::Direksiya => __('roles.direksiya'),
            self::Expert => __('roles.expert'),
            self::Authority => __('roles.authority'),
            self::Admin => __('roles.admin'),
        };
    }

    /**
     * Dashboard panel title for the role.
     */
    public function panelTitle(): string
    {
        return match ($this) {
            self::Applicant => __('panels.applicant'),
            self::Direksiya => __('panels.direksiya'),
            self::Expert => __('panels.expert'),
            self::Authority => __('panels.authority'),
            self::Admin => __('panels.admin'),
        };
    }

    /**
     * Whether this role is part of the staff (non-applicant) side.
     */
    public function isStaff(): bool
    {
        return $this !== self::Applicant;
    }
}

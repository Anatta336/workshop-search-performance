import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface OrderLine {
    id: number;
    product_id: number | null;
    product_name?: string;
    quantity: number;
    unit_price_pence: number;
}

export interface Order {
    id: number;
    customer_id: number;
    placed_at?: string | null;
    lines?: OrderLine[];
}

export interface Customer {
    id: number;
    first_name: string;
    last_name: string;
    email?: string | null;
    phone?: string | null;
    address?: string | null;
    city?: string | null;
    postal_code?: string | null;
    country?: string | null;
    orders?: Order[];
}

export type Paginated<T> = {
    data: T[];
    links?: Array<{ url: string | null; label: string; active: boolean }>;
    meta?: Record<string, any>;
};

export type BreadcrumbItemType = BreadcrumbItem;

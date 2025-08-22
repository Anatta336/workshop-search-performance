<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type AppPageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

type Metrics = {
    customers_count: number;
    orders_count: number;
    items_ordered_count: number;
    total_value_pence: number;
};

const page = usePage<AppPageProps<{ metrics?: Metrics }>>();
const metrics = computed(() => page.props.metrics ?? {
    customers_count: 0,
    orders_count: 0,
    items_ordered_count: 0,
    total_value_pence: 0,
});

function formatNumber(num: number): string {
    return new Intl.NumberFormat('en-GB').format(num);
}

function formatPence(pence: number): string {
    // Format pence as GBP with grouping separators and two decimals.
    // Using an English locale that produces comma group separators for thousands.
    // Assumption: the desired style is English grouping with GBP symbol (e.g. "£1,500,123.34").
    const value = pence / 100;
    return new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP' }).format(value);
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="flex items-center justify-between gap-4 rounded-xl border border-sidebar-border/70 bg-card p-4">
                    <div>
                        <div class="text-xs text-muted-foreground">Customers</div>
                        <div class="text-2xl font-semibold">{{ formatNumber(metrics.customers_count) }}</div>
                    </div>
                    <div class="text-sm text-muted-foreground">
                        <Link href="/customers">View</Link>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-4 rounded-xl border border-sidebar-border/70 bg-card p-4">
                    <div>
                        <div class="text-xs text-muted-foreground">Orders</div>
                        <div class="text-2xl font-semibold">{{ formatNumber(metrics.orders_count) }}</div>
                    </div>
                    <div class="text-sm text-muted-foreground">
                        <Link href="/orders">View</Link>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-4 rounded-xl border border-sidebar-border/70 bg-card p-4">
                    <div>
                        <div class="text-xs text-muted-foreground">Items Ordered</div>
                        <div class="text-2xl font-semibold">{{ formatNumber(metrics.items_ordered_count) }}</div>
                    </div>
                    <div class="text-sm text-muted-foreground">&nbsp;</div>
                </div>
            </div>

            <div class="mt-4 flex items-center gap-4 rounded-xl border border-sidebar-border/70 bg-card p-4">
                <div>
                    <div class="text-xs text-muted-foreground">Total Value</div>
                    <div class="text-2xl font-semibold">{{ formatPence(metrics.total_value_pence) }}</div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

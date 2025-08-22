<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { AppPageProps, Paginated } from '@/types';

type OrderItem = {
    id: number;
    customer?: { id: number; first_name: string; last_name: string } | null;
    lines_count?: number;
    total_value_pence?: number;
};

const page = usePage<AppPageProps<{ orders?: Paginated<OrderItem> }>>();
const orders = computed(() => (page.props.orders ?? { data: [] } as Paginated<OrderItem>));

const breadcrumbs = [
    { title: 'Orders', href: '/orders' },
];

function formatPence(pence: number | undefined): string {
    const value = (pence ?? 0) / 100;
    return new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP' }).format(value);
}
</script>

<template>
    <Head title="Orders" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <h1 class="text-2xl font-semibold">Orders</h1>

            <div class="mt-4">
                <table class="min-w-full table-fixed">
                    <thead>
                        <tr class="text-left">
                            <th class="px-2 py-1">Customer</th>
                            <th class="px-2 py-1">Lines</th>
                            <th class="px-2 py-1">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in orders.data" :key="order.id" class="border-t">
                            <td class="px-2 py-2">
                                <template v-if="order.customer">
                                    <Link :href="route('customers.show', order.customer.id)">{{ order.customer.first_name }} {{ order.customer.last_name }}</Link>
                                </template>
                                <template v-else>
                                    —
                                </template>
                            </td>
                            <td class="px-2 py-2">{{ order.lines_count ?? 0 }}</td>
                            <td class="px-2 py-2">{{ formatPence(order.total_value_pence) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4">
                    <div v-if="orders.links" class="flex gap-2">
                        <template v-for="(link, idx) in orders.links" :key="idx">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                v-html="link.label"
                                class="px-2 py-1 rounded border text-sm"
                            />
                            <span v-else class="px-2 py-1 text-sm text-neutral-500" v-html="link.label"></span>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

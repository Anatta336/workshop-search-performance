<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import type { AppPageProps, Customer } from '@/types';

const page = usePage<AppPageProps<{ customer?: Customer }>>();
const customer = computed(() => (page.props.customer ?? ({} as Customer)));

const breadcrumbs = [
    { title: 'Customers', href: '/customers' },
    { title: `${customer.value.first_name} ${customer.value.last_name}`, href: `/customers/${customer.value.id}` },
];
</script>

<template>
    <Head :title="`${customer.first_name} ${customer.last_name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <h1 class="text-2xl font-semibold">{{ customer.first_name }} {{ customer.last_name }}</h1>

            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded border p-4">
                    <h2 class="font-medium">Contact</h2>
                    <p class="mt-2">Email: {{ customer.email }}</p>
                    <p class="mt-1">Phone: {{ customer.phone }}</p>
                    <p class="mt-1">{{ customer.address }}</p>
                    <p class="mt-1">{{ customer.city }} {{ customer.postal_code }}, {{ customer.country }}</p>
                </div>

                <div class="rounded border p-4">
                    <h2 class="font-medium">Orders</h2>
                    <table class="min-w-full mt-2">
                        <thead>
                            <tr class="text-left"><th class="px-2 py-1">Placed</th><th class="px-2 py-1">Items</th><th class="px-2 py-1">Total</th></tr>
                        </thead>
                        <tbody>
                            <tr v-for="order in customer.orders ?? []" :key="order.id" class="border-t">
                                <td class="px-2 py-2">{{ order.placed_at ? new Date(order.placed_at).toLocaleString() : '' }}</td>
                                <td class="px-2 py-2">{{ (order.lines ?? []).length }}</td>
                                <td class="px-2 py-2">£{{ (((order.lines ?? []) as Array<any>).reduce((sum: number, l: any) => sum + (l.unit_price_pence * l.quantity), 0) / 100).toFixed(2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

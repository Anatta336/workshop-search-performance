<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { AppPageProps, Paginated, Customer } from '@/types';

const page = usePage<AppPageProps<{ customers?: Paginated<Customer> }>>();
const customers = computed(() => (page.props.customers ?? { data: [] } as Paginated<Customer>));

const breadcrumbs = [
    { title: 'Customers', href: '/customers' },
];
</script>

<template>
    <Head title="Customers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <h1 class="text-2xl font-semibold">Customers</h1>

            <div class="mt-4">
                <table class="min-w-full table-fixed">
                    <thead>
                        <tr class="text-left">
                            <th class="px-2 py-1">Name</th>
                            <th class="px-2 py-1">Email</th>
                            <th class="px-2 py-1">City</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="customer in customers.data" :key="customer.id" class="border-t">
                            <td class="px-2 py-2">
                                <Link :href="route('customers.show', customer.id)">{{ customer.first_name }} {{ customer.last_name }}</Link>
                            </td>
                            <td class="px-2 py-2">{{ customer.email }}</td>
                            <td class="px-2 py-2">{{ customer.city }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4">
                    <!-- simple pagination links if available -->
                    <div v-if="customers.links" class="flex gap-2">
                        <template v-for="(link, idx) in customers.links" :key="idx">
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

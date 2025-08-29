<script setup lang="ts">
import { ref, watch, onUnmounted, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

type CustomerSearchResult = {
    id: number;
    first_name?: string;
    last_name?: string;
    full_name: string;
    total_value_pence: number;
};

const query = ref('');
const results = ref<CustomerSearchResult[]>([]);
const loading = ref(false);
const cheat = ref(true);

let debounceTimer: ReturnType<typeof setTimeout> | null = null;
let abortController: AbortController | null = null;

const hasQuery = computed(() => query.value.trim().length > 0);
const showNoResults = computed(() => !loading.value && hasQuery.value && results.value.length === 0);

const currencyFormatter = new Intl.NumberFormat('en-GB', {
    style: 'currency',
    currency: 'GBP'
});

watch(query, (searchTerm: string) => {
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }
    debounceTimer = setTimeout(() => fetchResults(searchTerm.trim(), cheat.value), 150);
});

function customerUrl(id: number): string {
    return route('customers.show', id);
}

function searchUrl(searchTerm: string, cheatFlag: boolean): string {
    return route('customers.search', { q: searchTerm, cheat_to_make_faster: cheatFlag });
}

async function fetchResults(searchTerm: string, cheatFlag: boolean): Promise<void> {
    if (!searchTerm) {
        if (abortController) {
            abortController.abort();
            abortController = null;
        }
        results.value = [];
        loading.value = false;
        return;
    }

    if (abortController) {
        abortController.abort();
    }

    abortController = new AbortController();
    const controller = abortController;
    const signal = controller.signal;

    loading.value = true;
    try {
        const url = searchUrl(searchTerm, cheatFlag);
        const response = await fetch(url, { credentials: 'same-origin', signal });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const json = await response.json();
        results.value = (json.customers ?? []) as CustomerSearchResult[];
    } catch (error) {
        if (error instanceof Error && error.name === 'AbortError') {
            return;
        }
        results.value = [];
        console.warn('Search request failed:', error);
    } finally {
        if (abortController === controller) {
            abortController = null;
        }
        loading.value = false;
    }
}

function formatPence(pence: number): string {
    return currencyFormatter.format((pence ?? 0) / 100);
}

onUnmounted(() => {
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }
    if (abortController) {
        abortController.abort();
    }
});
</script>

<template>
    <div class="w-full max-w-md">
        <label for="customer-search" class="block text-sm font-medium text-muted-foreground">
            Search Customers
        </label>

        <div class="mt-2 flex items-center gap-2">
            <input
                id="cheat"
                v-model="cheat"
                type="checkbox"
                class="h-4 w-4 rounded border"
            />
            <label for="cheat" class="text-sm text-muted-foreground">
                Experimental AI search acceleration ‧₊˚✧
            </label>
        </div>

        <input
            id="customer-search"
            v-model="query"
            type="text"
            class="mt-1 block w-full rounded-md border p-2"
            placeholder="Type a name..."
            aria-label="Search customers"
        />

        <div class="mt-2 rounded border bg-card p-2">
            <div v-if="loading" class="text-sm text-muted-foreground">
                Searching…
            </div>
            <div v-else-if="showNoResults" class="text-sm text-muted-foreground">
                No results found
            </div>
            <ul v-else-if="results.length > 0" class="divide-y">
                <li v-for="customer in results" :key="customer.id" class="py-2">
                    <Link
                        :href="customerUrl(customer.id)"
                        class="flex items-center justify-between hover:bg-muted/50 rounded p-1 -m-1 transition-colors"
                    >
                        <span class="font-medium">{{ customer.full_name }}</span>
                        <span class="text-sm text-muted-foreground">
                            {{ formatPence(customer.total_value_pence) }}
                        </span>
                    </Link>
                </li>
            </ul>
        </div>
    </div>
</template>

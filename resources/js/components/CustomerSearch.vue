<script setup lang="ts">
import { ref, watch, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

type CustomerSearchResult = {
    id: number;
    first_name?: string;
    last_name?: string;
    full_name: string;
    total_value_pence: number;
};

const query = ref<string>('');
const results = ref<CustomerSearchResult[]>([]);
const loading = ref<boolean>(false);
const cheat = ref<boolean>(true);

let timer: number | null = null;
let currentController: AbortController | null = null;

watch(query, (searchTerm: string) => {
    if (timer) clearTimeout(timer);
    timer = window.setTimeout(() => fetchResults(searchTerm, cheat.value), 150) as unknown as number;
});

function customerUrl(id: number) {
    return route('customers.show', id) as string;
}

function searchUrl(searchTerm: string, cheatFlag: boolean) {
    return route('customers.search', { q: searchTerm, cheat_to_make_faster: cheatFlag }) as string;
}

async function fetchResults(searchTerm: string, cheatFlag: boolean) {
    if (!searchTerm) {
        if (currentController) {
            currentController.abort();
            currentController = null;
        }
        results.value = [];
        loading.value = false;
        return;
    }

    if (currentController) {
        currentController.abort();
    }

    currentController = new AbortController();
    const controller = currentController;
    const signal = controller.signal;

    loading.value = true;
    try {
        const url = searchUrl(searchTerm, cheatFlag);
        const res = await fetch(url, { credentials: 'same-origin', signal });
        if (!res.ok) {
            results.value = [];
            return;
        }
        const json = await res.json();
        results.value = (json.data ?? []) as CustomerSearchResult[];
    } catch (err) {
        if ((err as any)?.name === 'AbortError') {
            return;
        }
        results.value = [];
    } finally {
        // Only clear the shared reference if it still points to this
        // fetch's controller. Another fetch may have replaced
        // `currentController` while this one was running.
        if (currentController === controller) {
            currentController = null;
        }
        loading.value = false;
    }
}

function formatPence(pence: number) {
    return new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP' }).format((pence ?? 0) / 100);
}

onUnmounted(() => {
    if (timer) {
        clearTimeout(timer);
    }
    if (currentController) {
        currentController.abort();
    }
});
</script>

<template>
    <div class="w-full max-w-md">
        <label class="block text-sm font-medium text-muted-foreground">Search Customers</label>
        <div class="mt-2 flex items-center gap-2">
            <input id="cheat" type="checkbox" v-model="cheat" class="h-4 w-4 rounded border" />
            <label for="cheat" class="text-sm text-muted-foreground">Experimental AI search acceleration  ‧₊˚✧</label>
        </div>

        <input
            v-model="query"
            type="text"
            class="mt-1 block w-full rounded-md border p-2"
            placeholder="Type a name..."
            aria-label="Search customers"
        />

        <div class="mt-2 rounded border bg-card p-2">
            <div v-if="loading" class="text-sm text-muted-foreground">Searching…</div>
            <div v-else-if="results.length === 0 && query" class="text-sm text-muted-foreground">No results</div>
            <ul v-else class="divide-y">
                <li v-for="c in results" :key="c.id" class="py-2">
                    <Link :href="customerUrl(c.id)" class="flex justify-between items-center">
                        <span>{{ c.full_name }}</span>
                        <small class="text-muted-foreground">{{ formatPence(c.total_value_pence) }}</small>
                    </Link>
                </li>
            </ul>
        </div>
    </div>
</template>

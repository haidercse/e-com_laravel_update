<script setup>
import { computed, onMounted, ref } from 'vue';
import { useHead } from '@unhead/vue';
import { useRoute } from 'vue-router';
import { chatApi } from '../services/endpoints';
import { useAuth } from '../services/auth';

const route = useRoute();
const auth = useAuth();
const userId = computed(() => auth.user.value?.id || localStorage.getItem('ecom_user_id'));
const conversations = ref([]);
const messages = ref([]);
const currentId = ref('');
const text = ref('');
const createForm = ref({ product_id: '', title: '', message: '' });
const error = ref('');
const sending = ref(false);
const newMessageId = computed(() => messages.value.reduce((max, message) => Math.max(max, Number(message.id || 0)), 0));

useHead({ title: 'Messages — FlixShop' });

onMounted(async () => {
  if (!userId.value) { error.value = 'Sign in to view messages.'; return; }
  if (route.query.product) createForm.value.product_id = String(route.query.product);
  await loadConversations();
});

function listOf(payload) { return payload?.data?.data || payload?.data || []; }
function errorText(problem) { return problem.response?.data?.message || problem.message || 'Request failed.'; }

async function loadConversations() {
  try {
    const { data } = await chatApi.conversations(userId.value);
    conversations.value = listOf(data);
    if (!currentId.value && conversations.value.length && !route.query.product) await openConversation(conversations.value[0]);
  } catch (problem) { error.value = errorText(problem); }
}

async function openConversation(conversation) {
  currentId.value = String(conversation.id);
  try {
    const { data } = await chatApi.messages(conversation.id);
    messages.value = listOf(data).slice().reverse();
  } catch (problem) { error.value = errorText(problem); }
}

async function refreshMessages() {
  if (!currentId.value) return;
  try {
    const { data } = await chatApi.newMessages(currentId.value, newMessageId.value);
    const incoming = listOf(data);
    messages.value.push(...incoming.filter((message) => !messages.value.some((existing) => existing.id === message.id)));
  } catch (problem) { error.value = errorText(problem); }
}

async function sendMessage() {
  if (!text.value.trim() || !currentId.value) return;
  sending.value = true;
  error.value = '';
  try {
    const { data } = await chatApi.send({ conversation_id: currentId.value, user_id: userId.value, message: text.value.trim() });
    const sent = listOf(data);
    messages.value.push(...sent);
    text.value = '';
  } catch (problem) { error.value = errorText(problem); }
  finally { sending.value = false; }
}

async function createConversation() {
  if (!createForm.value.product_id || !createForm.value.message.trim()) return;
  sending.value = true;
  error.value = '';
  try {
    const { data } = await chatApi.create({ ...createForm.value, user_id: userId.value });
    if (!data?.result) throw new Error(data?.message || 'Could not start conversation.');
    await loadConversations();
    const conversation = conversations.value.find((item) => String(item.id) === String(data.conversation_id));
    if (conversation) await openConversation(conversation);
    createForm.value = { product_id: '', title: '', message: '' };
  } catch (problem) { error.value = errorText(problem); }
  finally { sending.value = false; }
}
</script>

<template>
  <section class="mx-auto max-w-6xl">
    <header class="mb-5"><p class="text-xs font-bold uppercase tracking-wider text-brand">Customer care</p><h1 class="mt-1 text-3xl font-extrabold text-ink">Messages</h1><p class="mt-2 text-sm text-muted">Talk with a shop about one of its products.</p></header>
    <p v-if="error" class="mb-4 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-900" role="alert">{{ error }}</p>
    <div class="grid min-h-[560px] overflow-hidden rounded-2xl bg-white shadow-card md:grid-cols-[280px_minmax(0,1fr)]">
      <aside class="border-b border-gray-100 md:border-b-0 md:border-r">
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-4"><h2 class="font-bold text-ink">Conversations</h2><button class="text-xs font-bold text-brand" type="button" @click="currentId = ''">New</button></div>
        <button v-for="item in conversations" :key="item.id" class="block w-full border-b border-gray-50 px-4 py-4 text-left hover:bg-brand-tint" :class="String(item.id) === currentId ? 'bg-brand-tint' : ''" type="button" @click="openConversation(item)"><span class="block truncate text-sm font-semibold text-ink">{{ item.shop_name }}</span><span class="mt-1 block truncate text-xs text-muted">{{ item.title }}</span></button>
        <p v-if="!conversations.length" class="p-4 text-sm text-muted">No conversations yet.</p>
      </aside>
      <section class="flex min-w-0 flex-col">
        <template v-if="currentId">
          <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4"><h2 class="font-bold text-ink">{{ conversations.find((item) => String(item.id) === currentId)?.title || 'Conversation' }}</h2><button class="text-xs font-semibold text-brand" type="button" @click="refreshMessages">Refresh</button></div>
          <div class="flex-1 space-y-3 overflow-y-auto bg-gray-50/70 p-5">
            <article v-for="message in messages" :key="message.id" class="max-w-[85%] rounded-xl px-4 py-3 text-sm" :class="String(message.user_id) === String(userId) ? 'ml-auto bg-brand text-white' : 'bg-white text-ink shadow-sm'"><p class="whitespace-pre-wrap">{{ message.message }}</p><p class="mt-1 text-right text-[10px] opacity-70">{{ message.date }} {{ message.time }}</p></article>
            <p v-if="!messages.length" class="py-10 text-center text-sm text-muted">Send a message to get the conversation started.</p>
          </div>
          <form class="flex gap-2 border-t border-gray-100 p-4" @submit.prevent="sendMessage"><input v-model="text" class="min-w-0 flex-1 rounded-full border border-gray-200 px-4 py-3 text-sm outline-none focus:border-brand" placeholder="Write a message…"/><button class="rounded-full bg-brand px-5 text-sm font-bold text-white disabled:opacity-50" :disabled="sending || !text.trim()">Send</button></form>
        </template>
        <form v-else class="m-auto w-full max-w-md space-y-4 p-6" @submit.prevent="createConversation"><div><h2 class="text-xl font-bold text-ink">Start a conversation</h2><p class="mt-1 text-sm text-muted">Enter the product ID and your message.</p></div><label class="block text-sm font-semibold">Product ID<input v-model.trim="createForm.product_id" required class="mt-1.5 w-full rounded-lg border border-gray-200 px-3 py-2.5 font-normal"/></label><label class="block text-sm font-semibold">Subject<input v-model.trim="createForm.title" class="mt-1.5 w-full rounded-lg border border-gray-200 px-3 py-2.5 font-normal"/></label><label class="block text-sm font-semibold">Message<textarea v-model.trim="createForm.message" required rows="4" class="mt-1.5 w-full rounded-lg border border-gray-200 px-3 py-2.5 font-normal"></textarea></label><button class="w-full rounded-lg bg-brand py-3 font-bold text-white disabled:opacity-50" :disabled="sending">Start conversation</button></form>
      </section>
    </div>
  </section>
</template>

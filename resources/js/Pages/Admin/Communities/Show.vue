<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <!-- Header with Back Button -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <Link
            :href="route('admin.communities.index')"
            class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
          >
            ← Back
          </Link>
          <div>
            <h1 class="text-3xl font-bold text-slate-900">Community Details</h1>
            <p class="mt-1 text-sm text-slate-600">View and manage community information</p>
          </div>
        </div>
      </div>

      <!-- Community Profile Card -->
      <div class="rounded-xl bg-white p-8 shadow-sm border border-slate-200">
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-6">
            <div class="h-20 w-20 flex-shrink-0">
              <img
                v-if="community.banner_url"
                :src="community.banner_url"
                :alt="community.name"
                class="h-20 w-20 rounded-full object-cover"
              />
              <div
                v-else
                class="flex h-20 w-20 items-center justify-center rounded-full bg-slate-900 text-2xl font-semibold text-white"
              >
                {{ getCommunityInitials(community.name) }}
              </div>
            </div>
            <div>
              <h2 class="text-2xl font-bold text-slate-900">{{ community.name }}</h2>
              <p class="mt-1 text-sm text-slate-600">{{ community.slug }}</p>
              <div class="mt-3 flex items-center gap-3">
                <span
                  :class="{
                    'bg-green-100 text-green-800': community.status === 'active',
                    'bg-gray-100 text-gray-800': community.status === 'archived',
                  }"
                  class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                >
                  {{ community.status }}
                </span>
                <span
                  :class="{
                    'bg-blue-100 text-blue-800': community.type === 'public',
                    'bg-purple-100 text-purple-800': community.type === 'private',
                  }"
                  class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                >
                  {{ community.type }}
                </span>
                <span v-if="community.category" class="inline-flex rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-800">
                  {{ community.category.name }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Community Stats -->
        <div class="mt-8 grid grid-cols-4 gap-6 border-t border-slate-200 pt-6">
          <div>
            <p class="text-sm font-medium text-slate-600">Total Members</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ community.members_count || 0 }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-slate-600">Admins</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ community.admins?.length || 0 }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-slate-600">Moderators</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ community.moderators?.length || 0 }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-slate-600">Discord Server</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ community.discord_server ? 1 : 0 }}</p>
          </div>
        </div>

        <!-- Description -->
        <div v-if="community.description" class="mt-6 border-t border-slate-200 pt-6">
          <p class="text-sm font-medium text-slate-600">Description</p>
          <p class="mt-2 text-sm text-slate-700">{{ community.description }}</p>
        </div>
      </div>

      <!-- Tabs -->
      <div class="border-b border-slate-200">
        <nav class="-mb-px flex space-x-8">
          <button
            @click="activeTab = 'overview'"
            :class="{
              'border-slate-900 text-slate-900': activeTab === 'overview',
              'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700': activeTab !== 'overview',
            }"
            class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium"
          >
            Overview
          </button>
          <button
            @click="activeTab = 'members'"
            :class="{
              'border-slate-900 text-slate-900': activeTab === 'members',
              'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700': activeTab !== 'members',
            }"
            class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium"
          >
            Members ({{ community.members_count }})
          </button>
          <button
            @click="activeTab = 'staff'"
            :class="{
              'border-slate-900 text-slate-900': activeTab === 'staff',
              'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700': activeTab !== 'staff',
            }"
            class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium"
          >
            Admins & Moderators
          </button>
          <button
            @click="activeTab = 'discord'"
            :class="{
              'border-slate-900 text-slate-900': activeTab === 'discord',
              'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700': activeTab !== 'discord',
            }"
            class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium"
          >
            Discord Server
          </button>
        </nav>
      </div>

      <!-- Overview Tab -->
      <div v-show="activeTab === 'overview'" class="space-y-6">
        <!-- Community Owner -->
        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <h3 class="text-lg font-semibold text-slate-900">Community Owner</h3>
          <div v-if="community.owner" class="mt-4 flex items-center gap-4">
            <div class="h-12 w-12 flex-shrink-0">
              <img
                v-if="community.owner.profile_image_url"
                :src="community.owner.profile_image_url"
                :alt="community.owner.name"
                class="h-12 w-12 rounded-full object-cover"
              />
              <div
                v-else
                class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white"
              >
                {{ getUserInitials(community.owner.name) }}
              </div>
            </div>
            <div class="flex-1">
              <p class="font-semibold text-slate-900">{{ community.owner.name }}</p>
              <p class="text-sm text-slate-600">@{{ community.owner.username }}</p>
              <p class="text-sm text-slate-500">{{ community.owner.email }}</p>
            </div>
            <Link
              :href="route('admin.users.show', community.owner.id)"
              class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800"
            >
              View Profile
            </Link>
          </div>
          <div v-else class="mt-4 text-sm text-slate-500">
            No owner assigned
          </div>
        </div>

        <!-- Community Info -->
        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <h3 class="text-lg font-semibold text-slate-900">Community Information</h3>
          <dl class="mt-4 grid grid-cols-2 gap-4">
            <div>
              <dt class="text-sm font-medium text-slate-600">Created</dt>
              <dd class="mt-1 text-sm text-slate-900">{{ formatDate(community.created_at) }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-slate-600">Last Updated</dt>
              <dd class="mt-1 text-sm text-slate-900">{{ formatDate(community.updated_at) }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-slate-600">Type</dt>
              <dd class="mt-1 text-sm text-slate-900 capitalize">{{ community.type }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-slate-600">Status</dt>
              <dd class="mt-1 text-sm text-slate-900 capitalize">{{ community.status }}</dd>
            </div>
            <div v-if="community.category" class="col-span-2">
              <dt class="text-sm font-medium text-slate-600">Category</dt>
              <dd class="mt-1 text-sm text-slate-900">{{ community.category.name }}</dd>
            </div>
          </dl>
        </div>
      </div>

      <!-- Members Tab -->
      <div v-show="activeTab === 'members'">
        <div class="rounded-xl bg-white shadow-sm border border-slate-200">
          <div class="p-6 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-900">Community Members</h3>
          </div>
          <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200">
              <thead class="bg-slate-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                    Member
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                    Role
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                    Joined
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                    Last Activity
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200 bg-white">
                <tr v-for="member in community.members" :key="member.id">
                  <td class="whitespace-nowrap px-6 py-4">
                    <div class="flex items-center">
                      <div class="h-10 w-10 flex-shrink-0">
                        <img
                          v-if="member.profile_image_url"
                          :src="member.profile_image_url"
                          :alt="member.name"
                          class="h-10 w-10 rounded-full object-cover"
                        />
                        <div
                          v-else
                          class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 text-xs font-semibold text-white"
                        >
                          {{ getUserInitials(member.name) }}
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="font-medium text-slate-900">{{ member.name }}</div>
                        <div class="text-sm text-slate-500">{{ member.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="whitespace-nowrap px-6 py-4">
                    <span
                      :class="{
                        'bg-purple-100 text-purple-800': member.role === 'admin',
                        'bg-blue-100 text-blue-800': member.role === 'moderator',
                        'bg-gray-100 text-gray-800': member.role === 'member',
                      }"
                      class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                    >
                      {{ member.role }}
                    </span>
                  </td>
                  <td class="whitespace-nowrap px-6 py-4">
                    <span
                      :class="{
                        'bg-green-100 text-green-800': member.member_status === 'active',
                        'bg-red-100 text-red-800': member.member_status === 'banned',
                        'bg-gray-100 text-gray-800': member.member_status === 'inactive',
                      }"
                      class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                    >
                      {{ member.member_status || 'active' }}
                    </span>
                  </td>
                  <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500">
                    {{ formatDate(member.joined_at) }}
                  </td>
                  <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500">
                    {{ member.last_activity_at ? formatDate(member.last_activity_at) : 'Never' }}
                  </td>
                  <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                    <div class="flex items-center justify-end gap-2">
                      <Link
                        :href="route('admin.users.show', member.id)"
                        class="text-slate-600 hover:text-slate-900"
                      >
                        View
                      </Link>
                      <button
                        v-if="member.member_status !== 'banned'"
                        @click="showBanMemberModal(member)"
                        class="text-orange-600 hover:text-orange-900"
                      >
                        Ban
                      </button>
                      <button
                        @click="showRemoveMemberModal(member)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Remove
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-if="!community.members || community.members.length === 0" class="p-6 text-center text-sm text-slate-500">
              No members found
            </div>
          </div>
        </div>
      </div>

      <!-- Staff Tab -->
      <div v-show="activeTab === 'staff'" class="space-y-6">
        <!-- Admins -->
        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <h3 class="text-lg font-semibold text-slate-900">Community Admins</h3>
          <div v-if="community.admins && community.admins.length > 0" class="mt-4 space-y-4">
            <div v-for="admin in community.admins" :key="admin.id" class="flex items-center justify-between border-b border-slate-200 pb-4 last:border-0">
              <div class="flex items-center gap-4">
                <div class="h-10 w-10 flex-shrink-0">
                  <img
                    v-if="admin.profile_image_url"
                    :src="admin.profile_image_url"
                    :alt="admin.name"
                    class="h-10 w-10 rounded-full object-cover"
                  />
                  <div
                    v-else
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 text-xs font-semibold text-white"
                  >
                    {{ getUserInitials(admin.name) }}
                  </div>
                </div>
                <div>
                  <p class="font-medium text-slate-900">{{ admin.name }}</p>
                  <p class="text-sm text-slate-500">@{{ admin.username }} • {{ admin.email }}</p>
                </div>
              </div>
              <Link
                :href="route('admin.users.show', admin.id)"
                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800"
              >
                View Profile
              </Link>
            </div>
          </div>
          <div v-else class="mt-4 text-sm text-slate-500">
            No admins assigned
          </div>
        </div>

        <!-- Moderators -->
        <div class="rounded-xl bg-white p-6 shadow-sm border border-slate-200">
          <h3 class="text-lg font-semibold text-slate-900">Community Moderators</h3>
          <div v-if="community.moderators && community.moderators.length > 0" class="mt-4 space-y-4">
            <div v-for="moderator in community.moderators" :key="moderator.id" class="flex items-center justify-between border-b border-slate-200 pb-4 last:border-0">
              <div class="flex items-center gap-4">
                <div class="h-10 w-10 flex-shrink-0">
                  <img
                    v-if="moderator.profile_image_url"
                    :src="moderator.profile_image_url"
                    :alt="moderator.name"
                    class="h-10 w-10 rounded-full object-cover"
                  />
                  <div
                    v-else
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 text-xs font-semibold text-white"
                  >
                    {{ getUserInitials(moderator.name) }}
                  </div>
                </div>
                <div>
                  <p class="font-medium text-slate-900">{{ moderator.name }}</p>
                  <p class="text-sm text-slate-500">@{{ moderator.username }} • {{ moderator.email }}</p>
                </div>
              </div>
              <Link
                :href="route('admin.users.show', moderator.id)"
                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800"
              >
                View Profile
              </Link>
            </div>
          </div>
          <div v-else class="mt-4 text-sm text-slate-500">
            No moderators assigned
          </div>
        </div>
      </div>

      <!-- Discord Tab -->
      <div v-show="activeTab === 'discord'">
        <div class="rounded-xl bg-white shadow-sm border border-slate-200">
          <div class="p-6 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-900">Connected Discord Server</h3>
          </div>
          <div v-if="community.discord_server" class="p-6">
            <div class="border border-slate-200 rounded-lg p-4">
              <div class="flex items-start justify-between">
                <div>
                  <h4 class="font-semibold text-slate-900">{{ community.discord_server.server_name }}</h4>
                  <p class="mt-1 text-sm text-slate-500">Server ID: {{ community.discord_server.server_id }}</p>
                  <p v-if="community.discord_server.invite_code" class="mt-1 text-sm text-slate-500">Invite Code: {{ community.discord_server.invite_code }}</p>
                  <p class="mt-2 text-xs text-slate-400">Connected: {{ formatDate(community.discord_server.created_at) }}</p>
                </div>
                <span
                  :class="{
                    'bg-green-100 text-green-800': community.discord_server.is_active,
                    'bg-gray-100 text-gray-800': !community.discord_server.is_active,
                  }"
                  class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                >
                  {{ community.discord_server.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>
            </div>
          </div>
          <div v-else class="p-6 text-center text-sm text-slate-500">
            No Discord server connected
          </div>
        </div>
      </div>
    </div>

    <!-- Ban Member Modal -->
    <Modal :show="banModalShow" @close="banModalShow = false">
      <div class="p-6">
        <h2 class="text-xl font-semibold text-slate-900">Ban Member</h2>
        <p class="mt-2 text-sm text-slate-600">
          Are you sure you want to ban <strong>{{ selectedMember?.name }}</strong> from this community?
        </p>
        <div class="mt-6 flex justify-end gap-3">
          <button
            @click="banModalShow = false"
            class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
          >
            Cancel
          </button>
          <button
            @click="banMember"
            :disabled="processing"
            class="rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <svg v-if="processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ processing ? 'Banning...' : 'Ban Member' }}</span>
          </button>
        </div>
      </div>
    </Modal>

    <!-- Remove Member Modal -->
    <Modal :show="removeModalShow" @close="removeModalShow = false">
      <div class="p-6">
        <h2 class="text-xl font-semibold text-slate-900">Remove Member</h2>
        <p class="mt-2 text-sm text-slate-600">
          Are you sure you want to remove <strong>{{ selectedMember?.name }}</strong> from this community?
        </p>
        <div class="mt-6 flex justify-end gap-3">
          <button
            @click="removeModalShow = false"
            class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
          >
            Cancel
          </button>
          <button
            @click="removeMember"
            :disabled="processing"
            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <svg v-if="processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ processing ? 'Removing...' : 'Remove Member' }}</span>
          </button>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  community: Object,
});

const activeTab = ref('overview');
const banModalShow = ref(false);
const removeModalShow = ref(false);
const selectedMember = ref(null);
const processing = ref(false);

const getUserInitials = (name) => {
  if (!name) return '?';
  const parts = name.split(' ');
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase();
  }
  return name.substring(0, 2).toUpperCase();
};

const getCommunityInitials = (name) => {
  if (!name) return '?';
  const parts = name.split(' ');
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase();
  }
  return name.substring(0, 2).toUpperCase();
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const showBanMemberModal = (member) => {
  selectedMember.value = member;
  banModalShow.value = true;
};

const showRemoveMemberModal = (member) => {
  selectedMember.value = member;
  removeModalShow.value = true;
};

const banMember = () => {
  if (!selectedMember.value) return;

  processing.value = true;
  router.post(
    route('admin.communities.members.ban', {
      community: props.community.id,
      user: selectedMember.value.id,
    }),
    {},
    {
      onSuccess: () => {
        banModalShow.value = false;
        selectedMember.value = null;
      },
      onFinish: () => {
        processing.value = false;
      },
    }
  );
};

const removeMember = () => {
  if (!selectedMember.value) return;

  processing.value = true;
  router.post(
    route('admin.communities.members.remove', {
      community: props.community.id,
      user: selectedMember.value.id,
    }),
    {},
    {
      onSuccess: () => {
        removeModalShow.value = false;
        selectedMember.value = null;
      },
      onFinish: () => {
        processing.value = false;
      },
    }
  );
};
</script>

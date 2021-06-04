<template>
  <tr>
    <td>
      <router-link
        :to="editRoute"
        class="no-underline dim text-primary font-bold"
      >
        {{ mailTemplate.name }}
      </router-link>
    </td>
    <td class="td-fit text-right pr-6 align-middle">
      <div class="inline-flex items-center">
        <span class="inline-flex">
          <router-link
            :to="editRoute"
            class="cursor-pointer text-70 hover:text-primary mr-3 inline-flex items-center"
            v-tooltip.click="__('Edit')"
            @click="deleteItem = mailTemplate.id"
          >
            <icon type="edit" width="22" height="18" view-box="0 0 22 16" />
          </router-link>
        </span>
        <button
          class="cursor-pointer text-70 hover:text-primary mr-3 inline-flex items-center"
          v-tooltip.click="__('Delete')"
          @click.prevent="openDeleteModal"
        >
          <icon type="delete" width="22" height="18" view-box="0 0 22 16" />
        </button>

        <portal to="modals" transition="fade-transition" v-if="deleteModalOpen">
          <delete-mail-template-modal
            v-if="deleteModalOpen"
            @confirm="confirmDelete"
            @close="closeDeleteModal"
          >
            <div class="p-8">
              <heading :level="2" class="mb-6">
                {{ __('Delete Mail Template') }}
              </heading>
              <p class="text-80 leading-normal">
                {{ __('Are you sure you want to delete this mail template?') }}
              </p>
            </div>
          </delete-mail-template-modal>
        </portal>
      </div>
    </td>
  </tr>
</template>

<script>
import DeleteMailTemplateModal from './DeleteMailTemplateModal.vue';

export default {
  components: {
    DeleteMailTemplateModal
  },
  props: {
    mailTemplate: {
      type: Object,
      required: true
    }
  },
  data: () => ({
    deleteModalOpen: false
  }),
  computed: {
    editRoute() {
      return {
        name: 'mail-edit',
        params: { id: this.mailTemplate.id }
      };
    }
  },
  methods: {
    openDeleteModal() {
      this.deleteModalOpen = true;
    },
    closeDeleteModal() {
      this.deleteModalOpen = false;
    },
    async confirmDelete() {
      // @todo
      await axios.post(
        `/nova-vendor/nova-mail-editor/mail-templates/${this.mailTemplate.id}/delete`
      );
      this.$emit('delete');
    }
  }
};
</script>

<style scoped>
svg {
  overflow: visible;
}
</style>

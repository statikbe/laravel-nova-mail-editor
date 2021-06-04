<template>
  <div>
    <mail-template-edit-form v-if="formData" :data="formData" />
    <div v-else>{{ __('Loading') }}</div>
  </div>
</template>

<script>
import MailTemplateEditForm from '../components/MailTemplateEditForm.vue';

export default {
  components: {
    MailTemplateEditForm
  },
  data: () => ({
    formData: null
  }),
  async created() {
    this.fetchData();
  },
  methods: {
    async fetchData() {
      this.formData = await this.getApiData(
        `/mail-templates/${this.$route.params.id}/edit`
      );
    }
  }
};
</script>

<style></style>

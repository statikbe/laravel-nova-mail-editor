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
      const formData = await this.getApiData(
        `/mail-templates/${this.$route.params.id}/edit`
      );

      this.$config.localizedFields.forEach(field => {
        Object.keys(formData[field] || {}).forEach(locale => {
          formData[`${field}___${locale}`] = formData[field][locale];
        });

        delete formData[field];
      });

      this.formData = formData;
    }
  }
};
</script>

<template>
  <div>
    <div class="card mb-8">
      <div class="flex border-b border-40" resource-id="13">
        <div class="w-1/5 px-8 py-6">
          <label
            for="template_id"
            class="inline-block text-80 pt-2 leading-tight"
          >
            {{ __('Mail type') }}
            <span class="text-danger text-sm">*</span>
          </label>
        </div>
        <div class="py-6 px-8 w-1/2">
          <select
            name="template_id"
            :id="id"
            v-model="selectedTemplate"
            class="w-full form-control form-select"
            required
          >
            <option value=""></option>
            <option :value="id" v-for="(name, id) in templates" :key="id">
              {{ name }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <div class="flex items-center">
      <router-link
        :to="{ name: 'mail-index' }"
        class="btn btn-link dim cursor-pointer text-80 ml-auto mr-6"
      >
        {{ __('Cancel') }}
      </router-link>
      <router-link
        :to="{ name: 'mail-create', params: { templateId: selectedTemplate } }"
        class="btn btn-default btn-primary inline-flex items-center relative mr-3"
        :event="selectedTemplate ? 'click' : ''"
        :disabled="!selectedTemplate"
      >
        {{ __('Continue') }}
      </router-link>
    </div>
  </div>
</template>

<script>
export default {
  data: () => ({
    selectedTemplate: '',
    templates: {}
  }),
  created() {
    this.fetchTemplates();
  },
  methods: {
    async fetchTemplates() {
      this.templates = await this.getApiData('/templates');
    }
  }
};
</script>

<style></style>

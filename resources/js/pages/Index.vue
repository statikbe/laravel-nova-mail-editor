<template>
  <div>
    <div class="flex">
      <heading class="mb-6 flex-no-shrink">
        {{ __('Mail Templates') }}
      </heading>
      <div class="w-full flex items-center mb-6">
        <div class="flex w-full justify-end items-center mx-3"></div>
        <div class="flex-no-shrink ml-auto">
          <router-link
            :to="{ name: 'mail-new' }"
            class="btn btn-default btn-primary"
          >
            {{ __('Create mail template') }}
          </router-link>
        </div>
      </div>
    </div>

    <div class="card">
      <table class="table w-full">
        <thead>
          <tr>
            <th class="text-left">
              Naam
            </th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <mail-template-table-row
            v-for="(mailTemplate, index) in mailTemplates"
            :mail-template="mailTemplate"
            :key="index"
            @delete="fetchMailTemplates"
          />
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import MailTemplateTableRow from '../components/MailTemplateTableRow';

export default {
  components: {
    MailTemplateTableRow
  },
  data: () => ({
    mailTemplates: [],
    deleteItem: null
  }),
  async created() {
    this.fetchMailTemplates();
  },
  methods: {
    async fetchMailTemplates() {
      this.mailTemplates = await this.getApiData('/mail-templates');
    }
  }
};
</script>

<style>
/* Scoped Styles */
</style>

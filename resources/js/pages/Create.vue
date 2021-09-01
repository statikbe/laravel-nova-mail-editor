<template>
  <mail-template-edit-form :data="mailTemplateData" action="create" />
</template>

<script>
import MailTemplateEditForm from '../components/MailTemplateEditForm.vue';

export default {
  components: {
    MailTemplateEditForm
  },
  data: () => ({
    mailTemplateData: {
      mail_class: null,
      design: null,
      name: null,
      subject: null,
      body: null,
      sender_name: null,
      sender_email: null,
      recipients: [],
      cc: [],
      bcc: [],
      attachments: []
    }
  })
  // created() {
  //   //  Initialize localized fields as objects with locale keys
  //   this.$config.localizedFields.forEach(fieldName => {
  //     const originalValue = this.mailTemplateData[fieldName];

  //     this.mailTemplateData[fieldName] = {};

  //     this.$config.locales.forEach(locale => {
  //       this.mailTemplateData[fieldName][locale] = originalValue;
  //     });
  //   });
  // }
};
</script>

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
      mail_class: '',
      name: '',
      subject: {},
      body: '',
      sender_name: '',
      sender_email: '',
      recipients: [],
      cc: [],
      bcc: [],
      attachments: []
    }
  })
};
</script>

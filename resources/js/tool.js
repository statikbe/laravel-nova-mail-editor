const path = require('path');
const Editor = require('vue-editor-js');

Nova.booting(async (Vue, router) => {
  router.addRoutes([
    {
      name: 'mail-index',
      path: '/mails',
      component: require('./pages/Index')
    },
    {
      name: 'mail-new',
      path: '/mails/new',
      component: require('./pages/New')
    },
    {
      name: 'mail-create',
      path: '/mails/:templateId/create',
      component: require('./pages/Create')
    },
    {
      name: 'mail-edit',
      path: '/mails/:id/edit',
      component: require('./pages/Edit')
    }
  ]);

  Vue.mixin({
    methods: {
      async getApiData(apiPath) {
        const { data } = await axios.get(
          path.join('/nova-vendor/nova-mail-editor', apiPath)
        );
        return data;
      },
      postFormData(apiPath, formData) {
        return axios({
          method: 'post',
          url: path.join('/nova-vendor/nova-mail-editor', apiPath),
          headers: { 'Content-Type': 'multipart/form-data' },
          data: formData
        });
      }
    }
  });

  Vue.prototype.$config = {
    locales: [],
    bodyFieldComponent: 'form-trix-field',
    localizedFields: [
      'sender_name',
      'sender_email',
      'subject',
      'body',
      'attachments'
    ]
  };

  Vue.use(Editor);

  const { data: locales } = await axios.get(
    '/nova-vendor/nova-mail-editor/locales'
  );

  Vue.prototype.$config.locales = locales;
});

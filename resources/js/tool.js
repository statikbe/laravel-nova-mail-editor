const path = require('path');
const Editor = require('vue-editor-js');

Nova.booting(async (Vue, router) => {
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

  // @todo config uit backend laten komen

  const { data } = await axios.get('/nova-vendor/nova-mail-editor/locales');

  Vue.prototype.$config = {
    locales: data,
    bodyFieldComponent: 'form-trix-field'
  };

  Vue.use(Editor);

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
});

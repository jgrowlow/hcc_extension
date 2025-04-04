import app from 'flarum/admin/app';

app.initializers.add('neoncrm-login', () => {
    app.extensionData
        .for('neoncrm-login')
        .registerSetting({
            setting: 'neoncrm-login.api_key',
            type: 'text',
            label: app.translator.trans('neoncrm-login.admin.settings.api_key_label'),
            help: 'Enter your NeonCRM API Key.',
        })
        .registerSetting({
            setting: 'neoncrm-login.redirect_url',
            type: 'text',
            label: app.translator.trans('neoncrm-login.admin.settings.redirect_url_label'),
            help: 'Enter the URL where users should be redirected after logging in.',
        });

    console.log('[NeonCRM Login] Admin settings loaded');
});

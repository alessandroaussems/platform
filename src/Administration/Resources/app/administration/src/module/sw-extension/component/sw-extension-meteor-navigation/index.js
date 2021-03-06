import template from './sw-extension-meteor-navigation.html.twig';
import './sw-extension-meteor-navigation.scss';

const { Component } = Shopware;

Component.register('sw-extension-meteor-navigation', {
    template,

    computed: {
        route() {
            return this.$route;
        },

        hasParentRoute() {
            return this.route.meta && this.route.meta.parentPath;
        },

        navigationPath() {
            if (!this.hasParentRoute) {
                return [];
            }

            return [{
                name: this.route.meta.parentPath,
                label: this.$tc('sw-extension-store.general.backBtn')
            }].reverse();
        },

        parentRoute() {
            return this.hasParentRoute ? this.navigationPath[0] : null;
        }
    }
});

<template>
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <span><img alt="image" class="img-circle" :src="imageMini"></span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear">
                                <span class="block m-t-xs">
                                    <strong class="font-bold">{{ user.first_name }} {{ user.last_name }} </strong>
                                </span>
                                <span class="text-muted text-xs block">
                                    {{ user.designation.name }}
                                    <b class="caret"/>
                                </span>
                            </span>
                        </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li>
                                <a href="/logout">
                                    <i class="fa fa-sign-out"/> Log out
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- TODO: will replace ACL rules here  -->
                <!-- start Menu -->
                <template>
                    <li style="font-size: smaller;" v-for="first_level_menu in firstLevelMenus()"
                    :class="first_level_menu.path.replace('/', '')"
                    v-if="first_level_menu.meta.label != '' ">
                        <router-link :to="first_level_menu.path" v-if="first_level_menu.beforeEnter">
                            <i :class="first_level_menu.meta.icon"/> {{label(first_level_menu)}}
                        </router-link>
                    </li>
                </template>
                <!-- end Menu -->
            </ul>
        </div>
    </nav>
</template>

<script>
"use strict";

export default {
    name: 'TheMenu',

    data: function () {
            return {
            user: this.$ep.user,
            imageMini: '/img/profile_small.jpg',
            access: '',
            acl_rules: (EP.settings.rule) ? EP.settings.rule : []
        }
    },

    methods: {
        menuChange() {
            let url_path = window.location.pathname
            let split_url = url_path.substr(1)
            let paths = split_url.split("/")


            let elem = $('#side-menu')

            elem.find('.active').removeClass('active')
            elem.find('.' + paths[0]).addClass('active')

            let elem3 = elem.find('.' + paths[0] + ' ul')
            let active_path = paths[0] + '_' + paths[1]

            if (paths.length > 2 && paths.length <= 3 && Number.isNaN(paths[2])) {
                let elem2 = elem.find('.' + paths[0] + ' .' + active_path + ' ul')
                elem2.addClass('in')
                elem2.parents().addClass('in')
                elem3.find('.' + active_path + '_' + paths[2]).addClass('active')
            } else {
                elem3.addClass('in')
            }

            elem3.find('.' + active_path).addClass('active')
        },

        menuClass(parent, child, level) {
            let parent_path = parent.path.replace('/', '')
            let child_path = ''
            if (child.path.substr(1).split('/')[1]) {
                child_path = child.path.substr(1).split('/')[level]
            } else {
                child_path = child.path.substr(1)
            }

            return parent_path + '_' + child_path
        },

        isMenu(item) {
            return item.meta.is_menu === undefined || item.meta.is_menu
        },

        isEnableFirstLevel(first) {

            return this.isUndefined(this.acl_rules) ||
                this.isUndefined(first.meta.rules) ||
                this.isUndefined(this.acl_rules[first.meta.rules]) ||
                this.acl_rules[first.meta.rules]['is_enable']
        },

        isEnableSecondLevel(first, second) {
            let child = 'child_rules'

            return this.isUndefined(this.acl_rules) ||
                this.isUndefined(first) ||
                this.isUndefined(this.acl_rules[first][child]) ||
                this.acl_rules[first][child][second.meta.rules] === undefined ||
                this.acl_rules[first][child][second.meta.rules]['is_enable']
        },

        isEnableThirdLevel(first, second, third) {
            let child = 'child_rules'

            return this.isUndefined(this.acl_rules) ||
                this.isUndefined(first) ||
                this.isUndefined(this.acl_rules[first][child]) ||
                this.acl_rules[first][child][second.meta.rules] === undefined ||
                this.acl_rules[first][child][second.meta.rules][child][third.meta.rules] === undefined ||
                this.acl_rules[first][child][second.meta.rules][child][third.meta.rules]['is_enable']
        },

        isUndefined(rules) {
            return rules === undefined
        },

        slashIfChildrenIsUndefined(item) {
            return item.children === undefined ? '/' : '#'
        },

        label(item) {
            return item.meta.label || 'No Label'
        },

        firstLevelMenus() {
            let routes = this.$router.options.routes
            let first_level_menus = routes.filter(first_level => this.isMenu(first_level))

            return first_level_menus.filter(first_level => this.isEnableFirstLevel(first_level))
        },

        secondLevelMenus(first_level_menu, second_level_menu) {
            let is_menu = second_level_menu.filter(menu => this.isMenu(menu))

            return is_menu.filter(menu => this.isEnableSecondLevel(first_level_menu, menu))
        },

        thirdLevelMenus(first_level_menu, second_level_menu, third_level_menu) {
            let is_menu = third_level_menu.filter(menu => this.isMenu(menu))

            return is_menu.filter(menu => this.isEnableThirdLevel(first_level_menu, second_level_menu, menu))
        },
    },

    created(){
        this.$constants.Default_API.get("/user/supervisor/manager/access")
        .then(response => {
            this.access = response.data;
            console.log(this.access);
        })
        .catch(error => {
            this.globalErrorHandling(error);
        });
    },

    mounted(){
        $('#side-menu').metisMenu()

        if(this.user.profile_pic){
            this.imageMini = 'https://vpssystem.s3.ap-southeast-2.amazonaws.com/customdb1/images/profile/'+this.user.profile_pic;
        }
    },

}
</script>

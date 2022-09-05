<template>
    <div id="wrapper">
        <the-menu/>
        <div id="page-wrapper" class="gray-bg" >
            <the-header/>
                <div class="row wrapper border-bottom white-bg m-t-n-lg">
                    <div class="col-md-4"><h2>{{ $route.meta.label }}</h2></div>
                    <div class="col-md-8">
                        <the-breadcrumb/>
                    </div>
                </div>
                <div class="wrapper wrapper-content animated fadeIn">
                    <router-view :key='$route.path'/>
                </div>
            <the-footer/>
        </div>
    </div>
</template>

<script>
"use strict"

import TheHeader from '@/layout/TheHeader'
import TheBreadcrumb from '@/layout/TheBreadcrumb'
import TheMenu from '@/layout/TheMenu'
import TheFooter from '@/layout/TheFooter'

export default {
    name: 'app',

    components: { TheHeader, TheBreadcrumb, TheMenu, TheFooter },
}
</script>

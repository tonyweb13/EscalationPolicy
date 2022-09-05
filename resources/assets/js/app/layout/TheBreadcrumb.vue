<template>
    <small class="pull-right m-t-md">
        <ol class="breadcrumb">
            <li v-for="breadcrumb in $route.matched">
                <i :class="breadcrumb.meta.icon" /> {{ breadcrumb.meta.label }}
            </li>
        </ol>
    </small>
</template>

<script>
"use strict";

export default {
    name: 'TheBreadcrumb',
}
</script>

<template>
    <div>
        <router-view :key='$route.path'/>
    </div>
</template>

<script>
"use strict"

export default {
    name: "BaseRouterView",
}
</script>

'use strict'

import Vue from 'vue'
import TheForm from '@/containers/TheForm.vue'
import TheLabel from '@/containers/TheLabel.vue'
import TheFormGroup from '@/containers/TheFormGroup.vue'
import TheIbox from '@/containers/TheIbox.vue'

let name_to_component = {
    TheForm,
    TheFormGroup,
    TheLabel,
    TheIbox,
}

Object.keys(name_to_component).forEach((name) => {
    Vue.component(name, name_to_component[name]);
});

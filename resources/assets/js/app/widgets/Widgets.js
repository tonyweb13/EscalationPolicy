'use strict'

import Vue from 'vue'
import ButtonAdd from '@/widgets/ButtonAdd.vue'
import ButtonCancel from '@/widgets/ButtonCancel.vue'
import ButtonClose from '@/widgets/ButtonClose.vue'
import ButtonSave from '@/widgets/ButtonSave.vue'
import ButtonSubmit from '@/widgets/ButtonSubmit.vue'
import ButtonCustom from '@/widgets/ButtonCustom.vue'
import InputText from '@/widgets/InputText.vue'
import InputNumber from '@/widgets/InputNumber.vue'
import InputTextarea from '@/widgets/InputTextarea.vue'
import InputFileUploader from '@/widgets/InputFileUploader.vue'
import InputSingleFileUploader from '@/widgets/InputSingleFileUploader.vue'
import InputRadio from '@/widgets/InputRadio.vue'
import InputCheckbox from '@/widgets/InputCheckbox.vue'
import LabelRadio from '@/widgets/labelRadio.vue'
import Modal from '@/widgets/Modal.vue'
import LoadingModal from '@/widgets/LoadingModal.vue'
import LoadingModalReport from '@/widgets/LoadingModalReport.vue'
import TheSpinner from '@/widgets/TheSpinner.vue'
import BehavioralManagementFlowchart from '@/widgets/BehavioralManagementFlowchart.vue'
import InputSearch from '@/widgets/InputSearch.vue'
import InputSearchClear from '@/widgets/InputSearchClear.vue'
import ModalCoachingLog from '@/widgets/ModalCoachingLog.vue'
import ModalNoClose from '@/widgets/ModalNoClose.vue'

let name_to_component = {
    ButtonAdd,
    ButtonCancel,
    ButtonClose,
    ButtonSave,
    ButtonSubmit,
    ButtonCustom,
    InputText,
    InputTextarea,
    InputFileUploader,
    InputSingleFileUploader,
    Modal,
    LoadingModal,
    LoadingModalReport,
    TheSpinner,
    InputRadio,
    InputCheckbox,
    LabelRadio,
    BehavioralManagementFlowchart,
    InputSearch,
    InputSearchClear,
    InputNumber,
    ModalCoachingLog,
    ModalNoClose
}

Object.keys(name_to_component).forEach((name) => {
    Vue.component(name, name_to_component[name]);
});

export default {
    props: [
        'label',
        'id',
        'value',
        'name',
        'required',
        'disabled',
        'placeholder',
        'asterisk',
        'checked',
        'vmodel',
        'type'
     ],
    methods: {
        onInput: function (event) {
            this.$emit('input', event.target.value);
        },
    },
}

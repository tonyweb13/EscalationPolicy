"use strict"

import Toastr from 'toastr';

const SLOW_FADE_SEC = 5000;

function tipmsg(msg, response) {
    window.hide_toastr = Toastr.remove;

    Toastr.options.timeOut = SLOW_FADE_SEC;
    switch(response) {
        case 'success':
            Toastr.success(msg);
            break;
        case 'error':
            Toastr.error(msg);
            break;
        case 'warning':
            Toastr.warning(msg);
            break;
        default:
            Toastr.success(msg);
    }
}

export function success(msg) {
    tipmsg(msg, 'success');
}

export function failure(msg) {
    tipmsg(msg, 'error');
}

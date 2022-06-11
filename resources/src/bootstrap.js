window._ = require("lodash");
_.mixin(require("lodash-inflection"));

window.moment = require("moment");

moment.fn.diffForHumans = function () {
    return `${moment.duration(moment().diff(this)).humanize()} ago`;
};

window.axios = require("axios");

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.axios.defaults.headers.common["Accept"] = "application/json";

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

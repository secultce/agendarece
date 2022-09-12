import Vue from 'vue';
import Vuetify from 'vuetify';
import pt from 'vuetify/lib/locale/pt';
import 'vuetify/dist/vuetify.min.css';

Vue.use(Vuetify);

const opts = {
    icons: {
        iconfont: 'fa',
    },
    lang: {
        locales: { pt },
        current: 'pt'
    }
}

export default new Vuetify(opts);
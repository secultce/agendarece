import Vue from 'vue';
import Vuetify from 'vuetify';
import pt from 'vuetify/lib/locale/pt';

Vue.use(Vuetify);

const opts = {
    icons: {
        iconfont: 'fa',
    },
    lang: {
        locales: { pt },
        current: 'pt'
    },
    theme: {
        themes: {
            light: {
                primary: "#2b60ac"
            }
        }
    }
}

export default new Vuetify(opts);
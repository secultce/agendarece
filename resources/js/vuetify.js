import Vue from 'vue';
import Vuetify from 'vuetify';
import pt from 'vuetify/lib/locale/pt';

Vue.use(Vuetify);

const opts = {
    customVariables: ['~/assets/variables.scss'],
    options: {
        customProperties: true
    },
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
                primary: $('body').css('--primary-color')
            }
        }
    }
}

export default new Vuetify(opts);
import Vue from 'vue'
import Router from 'vue-router'
import { interopDefault } from './utils'

const _2465c365 = () => interopDefault(import('../pages/agent/index.vue' /* webpackChunkName: "pages/agent/index" */))
const _d27e23a4 = () => interopDefault(import('../pages/butler/index.vue' /* webpackChunkName: "pages/butler/index" */))
const _27019a31 = () => interopDefault(import('../pages/butler/index/index.vue' /* webpackChunkName: "pages/butler/index/index" */))
const _3a569a7d = () => interopDefault(import('../pages/butler/index/enterprise_collect.vue' /* webpackChunkName: "pages/butler/index/enterprise_collect" */))
const _bfd0132a = () => interopDefault(import('../pages/butler/index/enterprise_info.vue' /* webpackChunkName: "pages/butler/index/enterprise_info" */))
const _890989a4 = () => interopDefault(import('../pages/butler/index/industry_concer.vue' /* webpackChunkName: "pages/butler/index/industry_concer" */))
const _5c722b16 = () => interopDefault(import('../pages/butler/index/problem_feedback.vue' /* webpackChunkName: "pages/butler/index/problem_feedback" */))
const _476fa50f = () => interopDefault(import('../pages/butler/index/support_info.vue' /* webpackChunkName: "pages/butler/index/support_info" */))
const _3bf0bc4c = () => interopDefault(import('../pages/certification/index.vue' /* webpackChunkName: "pages/certification/index" */))
const _4a38b697 = () => interopDefault(import('../pages/declaration.vue' /* webpackChunkName: "pages/declaration" */))
const _941ca04c = () => interopDefault(import('../pages/declaration/index.vue' /* webpackChunkName: "pages/declaration/index" */))
const _bed14a0e = () => interopDefault(import('../pages/declaration/detail.vue' /* webpackChunkName: "pages/declaration/detail" */))
const _6fdaf7ee = () => interopDefault(import('../pages/declaration/online/_mode.vue' /* webpackChunkName: "pages/declaration/online/_mode" */))
const _48f342c9 = () => interopDefault(import('../pages/login/index.vue' /* webpackChunkName: "pages/login/index" */))
const _249d62cb = () => interopDefault(import('../pages/notice.vue' /* webpackChunkName: "pages/notice" */))
const _b82d35e4 = () => interopDefault(import('../pages/notice/index.vue' /* webpackChunkName: "pages/notice/index" */))
const _3498ddfa = () => interopDefault(import('../pages/notice/activity.vue' /* webpackChunkName: "pages/notice/activity" */))
const _88172bf0 = () => interopDefault(import('../pages/notice/appropriation.vue' /* webpackChunkName: "pages/notice/appropriation" */))
const _757346b4 = () => interopDefault(import('../pages/notice/declare.vue' /* webpackChunkName: "pages/notice/declare" */))
const _617e97b6 = () => interopDefault(import('../pages/policy.vue' /* webpackChunkName: "pages/policy" */))
const _0c066a30 = () => interopDefault(import('../pages/policy/index.vue' /* webpackChunkName: "pages/policy/index" */))
const _b8ee0824 = () => interopDefault(import('../pages/policy/detail/index.vue' /* webpackChunkName: "pages/policy/detail/index" */))
const _eb5ea6da = () => interopDefault(import('../pages/policy/detail/explain.vue' /* webpackChunkName: "pages/policy/detail/explain" */))
const _63762059 = () => interopDefault(import('../pages/register/index.vue' /* webpackChunkName: "pages/register/index" */))
const _1e27c92f = () => interopDefault(import('../pages/reset/index.vue' /* webpackChunkName: "pages/reset/index" */))
const _030507bf = () => interopDefault(import('../pages/share/index.vue' /* webpackChunkName: "pages/share/index" */))
const _82d44ca8 = () => interopDefault(import('../pages/accountSet/updateEmail.vue' /* webpackChunkName: "pages/accountSet/updateEmail" */))
const _6174e98a = () => interopDefault(import('../pages/accountSet/updatePassword.vue' /* webpackChunkName: "pages/accountSet/updatePassword" */))
const _02dd927e = () => interopDefault(import('../pages/accountSet/updatePhone.vue' /* webpackChunkName: "pages/accountSet/updatePhone" */))
const _00a5b8ce = () => interopDefault(import('../pages/agent/detail.vue' /* webpackChunkName: "pages/agent/detail" */))
const _0958c46c = () => interopDefault(import('../pages/agent/dishonesty/index.vue' /* webpackChunkName: "pages/agent/dishonesty/index" */))
const _cc9155d8 = () => interopDefault(import('../pages/agent/evaluation_list.vue' /* webpackChunkName: "pages/agent/evaluation_list" */))
const _56fe6a78 = () => interopDefault(import('../pages/agent/notice_list.vue' /* webpackChunkName: "pages/agent/notice_list" */))
const _6d23d008 = () => interopDefault(import('../pages/agent/organ_detail.vue' /* webpackChunkName: "pages/agent/organ_detail" */))
const _613d72b8 = () => interopDefault(import('../pages/agent/serious_dishonesty.vue' /* webpackChunkName: "pages/agent/serious_dishonesty" */))
const _e65c693c = () => interopDefault(import('../pages/butler/utils.js' /* webpackChunkName: "pages/butler/utils" */))
const _084343de = () => interopDefault(import('../pages/register/agreement.vue' /* webpackChunkName: "pages/register/agreement" */))
const _92446432 = () => interopDefault(import('../pages/reset/resetSuccess.vue' /* webpackChunkName: "pages/reset/resetSuccess" */))
const _7d23ddb5 = () => interopDefault(import('../pages/share/activity/index.vue' /* webpackChunkName: "pages/share/activity/index" */))
const _8bda4eb2 = () => interopDefault(import('../pages/agent/dishonesty/detail.vue' /* webpackChunkName: "pages/agent/dishonesty/detail" */))
const _7bc88fc4 = () => interopDefault(import('../pages/butler/butler_components/industry_news.vue' /* webpackChunkName: "pages/butler/butler_components/industry_news" */))
const _036e143c = () => interopDefault(import('../pages/butler/butler_components/message_list.vue' /* webpackChunkName: "pages/butler/butler_components/message_list" */))
const _6e588f65 = () => interopDefault(import('../pages/butler/butler_components/option_detail.vue' /* webpackChunkName: "pages/butler/butler_components/option_detail" */))
const _18ae71b8 = () => interopDefault(import('../pages/share/activity/activity_detail.vue' /* webpackChunkName: "pages/share/activity/activity_detail" */))
const _101f4efb = () => interopDefault(import('../pages/share/activity/utils.js' /* webpackChunkName: "pages/share/activity/utils" */))
const _01d69b0f = () => interopDefault(import('../pages/agent/service_guide/_type.vue' /* webpackChunkName: "pages/agent/service_guide/_type" */))
const _0d3b45af = () => interopDefault(import('../pages/index.vue' /* webpackChunkName: "pages/index" */))
const _40228cf2 = () => interopDefault(import('../pages/index/index.vue' /* webpackChunkName: "pages/index/index" */))
const _22782a8e = () => interopDefault(import('../pages/index/complaint/index.vue' /* webpackChunkName: "pages/index/complaint/index" */))
const _7cb772f3 = () => interopDefault(import('../pages/index/footnote/index.vue' /* webpackChunkName: "pages/index/footnote/index" */))
const _29b7fb9f = () => interopDefault(import('../pages/index/guide/index.vue' /* webpackChunkName: "pages/index/guide/index" */))
const _4099a080 = () => interopDefault(import('../pages/index/infomations/index.vue' /* webpackChunkName: "pages/index/infomations/index" */))
const _12cce643 = () => interopDefault(import('../pages/index/infomations/index/index.vue' /* webpackChunkName: "pages/index/infomations/index/index" */))
const _01d59d6b = () => interopDefault(import('../pages/index/infomations/index/_id.vue' /* webpackChunkName: "pages/index/infomations/index/_id" */))
const _7be3a85a = () => interopDefault(import('../pages/index/personal/index.vue' /* webpackChunkName: "pages/index/personal/index" */))
const _4ddd6fd4 = () => interopDefault(import('../pages/index/personal/index/index.vue' /* webpackChunkName: "pages/index/personal/index/index" */))
const _7c84680a = () => interopDefault(import('../pages/index/personal/index/collection.vue' /* webpackChunkName: "pages/index/personal/index/collection" */))
const _b2982b82 = () => interopDefault(import('../pages/index/personal/index/mine.vue' /* webpackChunkName: "pages/index/personal/index/mine" */))
const _4afd1e3d = () => interopDefault(import('../pages/index/personal/index/record.vue' /* webpackChunkName: "pages/index/personal/index/record" */))
const _02d74300 = () => interopDefault(import('../pages/index/personal/index/record/index.vue' /* webpackChunkName: "pages/index/personal/index/record/index" */))
const _10763df0 = () => interopDefault(import('../pages/index/personal/index/revised_record.vue' /* webpackChunkName: "pages/index/personal/index/revised_record" */))
const _763c4043 = () => interopDefault(import('../pages/index/footnote/concat.vue' /* webpackChunkName: "pages/index/footnote/concat" */))
const _a20b1ee0 = () => interopDefault(import('../pages/index/footnote/statement.vue' /* webpackChunkName: "pages/index/footnote/statement" */))
const _180a5e20 = () => interopDefault(import('../pages/index/personal/detail.vue' /* webpackChunkName: "pages/index/personal/detail" */))

Vue.use(Router)

if (process.client) {
  if ('scrollRestoration' in window.history) {
    window.history.scrollRestoration = 'manual'

    // reset scrollRestoration to auto when leaving page, allowing page reload
    // and back-navigation from other pages to use the browser to restore the
    // scrolling position.
    window.addEventListener('beforeunload', () => {
      window.history.scrollRestoration = 'auto'
    })

    // Setting scrollRestoration to manual again when returning to this page.
    window.addEventListener('load', () => {
      window.history.scrollRestoration = 'manual'
    })
  }
}
const scrollBehavior = function (to, from, savedPosition) {
  // if the returned position is falsy or an empty object,
  // will retain current scroll position.
  let position = false

  // if no children detected and scrollToTop is not explicitly disabled
  if (
    to.matched.length < 2 &&
    to.matched.every(r => r.components.default.options.scrollToTop !== false)
  ) {
    // scroll to the top of the page
    position = { x: 0, y: 0 }
  } else if (to.matched.some(r => r.components.default.options.scrollToTop)) {
    // if one of the children has scrollToTop option set to true
    position = { x: 0, y: 0 }
  }

  // savedPosition is only available for popstate navigations (back button)
  if (savedPosition) {
    position = savedPosition
  }

  return new Promise((resolve) => {
    // wait for the out transition to complete (if necessary)
    window.$nuxt.$once('triggerScroll', () => {
      // coords will be used if no selector is provided,
      // or if the selector didn't match any element.
      if (to.hash) {
        let hash = to.hash
        // CSS.escape() is not supported with IE and Edge.
        if (typeof window.CSS !== 'undefined' && typeof window.CSS.escape !== 'undefined') {
          hash = '#' + window.CSS.escape(hash.substr(1))
        }
        try {
          if (document.querySelector(hash)) {
            // scroll to anchor by returning the selector
            position = { selector: hash }
          }
        } catch (e) {
          console.warn('Failed to save scroll position. Please add CSS.escape() polyfill (https://github.com/mathiasbynens/CSS.escape).')
        }
      }
      resolve(position)
    })
  })
}

export function createRouter() {
  return new Router({
    mode: 'history',
    base: decodeURI('/'),
    linkActiveClass: 'nuxt-link-active',
    linkExactActiveClass: 'nuxt-link-exact-active',
    scrollBehavior,

    routes: [{
      path: "/agent",
      component: _2465c365,
      name: "agent"
    }, {
      path: "/butler",
      component: _d27e23a4,
      children: [{
        path: "",
        component: _27019a31,
        name: "butler-index"
      }, {
        path: "enterprise_collect",
        component: _3a569a7d,
        name: "butler-index-enterprise_collect"
      }, {
        path: "enterprise_info",
        component: _bfd0132a,
        name: "butler-index-enterprise_info"
      }, {
        path: "industry_concer",
        component: _890989a4,
        name: "butler-index-industry_concer"
      }, {
        path: "problem_feedback",
        component: _5c722b16,
        name: "butler-index-problem_feedback"
      }, {
        path: "support_info",
        component: _476fa50f,
        name: "butler-index-support_info"
      }]
    }, {
      path: "/certification",
      component: _3bf0bc4c,
      name: "certification"
    }, {
      path: "/declaration",
      component: _4a38b697,
      children: [{
        path: "",
        component: _941ca04c,
        name: "declaration"
      }, {
        path: "detail",
        component: _bed14a0e,
        name: "declaration-detail"
      }, {
        path: "online/:mode?",
        component: _6fdaf7ee,
        name: "declaration-online-mode"
      }]
    }, {
      path: "/login",
      component: _48f342c9,
      name: "login"
    }, {
      path: "/notice",
      component: _249d62cb,
      children: [{
        path: "",
        component: _b82d35e4,
        name: "notice"
      }, {
        path: "activity",
        component: _3498ddfa,
        name: "notice-activity"
      }, {
        path: "appropriation",
        component: _88172bf0,
        name: "notice-appropriation"
      }, {
        path: "declare",
        component: _757346b4,
        name: "notice-declare"
      }]
    }, {
      path: "/policy",
      component: _617e97b6,
      children: [{
        path: "",
        component: _0c066a30,
        name: "policy"
      }, {
        path: "detail",
        component: _b8ee0824,
        name: "policy-detail"
      }, {
        path: "detail/explain",
        component: _eb5ea6da,
        name: "policy-detail-explain"
      }]
    }, {
      path: "/register",
      component: _63762059,
      name: "register"
    }, {
      path: "/reset",
      component: _1e27c92f,
      name: "reset"
    }, {
      path: "/share",
      component: _030507bf,
      name: "share"
    }, {
      path: "/accountSet/updateEmail",
      component: _82d44ca8,
      name: "accountSet-updateEmail"
    }, {
      path: "/accountSet/updatePassword",
      component: _6174e98a,
      name: "accountSet-updatePassword"
    }, {
      path: "/accountSet/updatePhone",
      component: _02dd927e,
      name: "accountSet-updatePhone"
    }, {
      path: "/agent/detail",
      component: _00a5b8ce,
      name: "agent-detail"
    }, {
      path: "/agent/dishonesty",
      component: _0958c46c,
      name: "agent-dishonesty"
    }, {
      path: "/agent/evaluation_list",
      component: _cc9155d8,
      name: "agent-evaluation_list"
    }, {
      path: "/agent/notice_list",
      component: _56fe6a78,
      name: "agent-notice_list"
    }, {
      path: "/agent/organ_detail",
      component: _6d23d008,
      name: "agent-organ_detail"
    }, {
      path: "/agent/serious_dishonesty",
      component: _613d72b8,
      name: "agent-serious_dishonesty"
    }, {
      path: "/butler/utils",
      component: _e65c693c,
      name: "butler-utils"
    }, {
      path: "/register/agreement",
      component: _084343de,
      name: "register-agreement"
    }, {
      path: "/reset/resetSuccess",
      component: _92446432,
      name: "reset-resetSuccess"
    }, {
      path: "/share/activity",
      component: _7d23ddb5,
      name: "share-activity"
    }, {
      path: "/agent/dishonesty/detail",
      component: _8bda4eb2,
      name: "agent-dishonesty-detail"
    }, {
      path: "/butler/butler_components/industry_news",
      component: _7bc88fc4,
      name: "butler-butler_components-industry_news"
    }, {
      path: "/butler/butler_components/message_list",
      component: _036e143c,
      name: "butler-butler_components-message_list"
    }, {
      path: "/butler/butler_components/option_detail",
      component: _6e588f65,
      name: "butler-butler_components-option_detail"
    }, {
      path: "/share/activity/activity_detail",
      component: _18ae71b8,
      name: "share-activity-activity_detail"
    }, {
      path: "/share/activity/utils",
      component: _101f4efb,
      name: "share-activity-utils"
    }, {
      path: "/agent/service_guide/:type?",
      component: _01d69b0f,
      name: "agent-service_guide-type"
    }, {
      path: "/",
      component: _0d3b45af,
      children: [{
        path: "",
        component: _40228cf2,
        name: "index"
      }, {
        path: "complaint",
        component: _22782a8e,
        name: "index-complaint"
      }, {
        path: "footnote",
        component: _7cb772f3,
        name: "index-footnote"
      }, {
        path: "guide",
        component: _29b7fb9f,
        name: "index-guide"
      }, {
        path: "infomations",
        component: _4099a080,
        children: [{
          path: "",
          component: _12cce643,
          name: "index-infomations-index"
        }, {
          path: ":id",
          component: _01d59d6b,
          name: "index-infomations-index-id"
        }]
      }, {
        path: "personal",
        component: _7be3a85a,
        children: [{
          path: "",
          component: _4ddd6fd4,
          name: "index-personal-index"
        }, {
          path: "collection",
          component: _7c84680a,
          name: "index-personal-index-collection"
        }, {
          path: "mine",
          component: _b2982b82,
          name: "index-personal-index-mine"
        }, {
          path: "record",
          component: _4afd1e3d,
          children: [{
            path: "",
            component: _02d74300,
            name: "index-personal-index-record"
          }]
        }, {
          path: "revised_record",
          component: _10763df0,
          name: "index-personal-index-revised_record"
        }]
      }, {
        path: "footnote/concat",
        component: _763c4043,
        name: "index-footnote-concat"
      }, {
        path: "footnote/statement",
        component: _a20b1ee0,
        name: "index-footnote-statement"
      }, {
        path: "personal/detail",
        component: _180a5e20,
        name: "index-personal-detail"
      }]
    }],

    fallback: false
  })
}

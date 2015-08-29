[globalVar = GP:type = {$plugin.pdfcontroller.pages.print.typeNum}]
  ## images
  tt_content.image {
    20 {
      renderMethod = simple
      rendering {
        simple {
          imageStdWrapNoWidth.wrap = |
          imageStdWrap.dataWrap = |
          caption.wrap = |
        }
      }
      layout {
        default {
          value (
            <table cellspacing="0" cellpadding="0" style="width: 100%; text-align: left; margin: 0;">
              <tr>
                <td valign="top" align="center" style="text-align: center;">
                  ###IMAGES###
                </td>
              </tr>
              <tr>
                <td valign="top">
                  ###TEXT###
                </td>
              </tr>
            </table>
)
          override >
        }
          // 150622, dwildt, +: intext-right-nowrap
        25 = TEXT
        25 {
          value (
            <table cellspacing="0" cellpadding="0" style="width: 100%; text-align: left; margin: 0;">
              <tr>
                <td valign="top">
                  ###TEXT###
                </td>
                <td valign="top"style="padding:0 10px 0 10px;">
                  ###IMAGES###
                </td>
              </tr>
            </table>
)
          override >
        }
          // 150622, dwildt, +: intext-left-nowrap
        26 = TEXT
        26 {
          value (
            <table cellspacing="0" cellpadding="0" style="width: 100%; text-align: left; margin: 0;">
              <tr>
                <td valign="top"style="padding:0 10px 0 10px;">
                  ###IMAGES###
                </td>
                <td valign="top">
                  ###TEXT###
                </td>
              </tr>
            </table>
)
          override >
        }
      }
    }
  }
[global]

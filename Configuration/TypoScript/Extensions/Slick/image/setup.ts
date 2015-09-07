[globalVar = GP:type = {$plugin.pdfcontroller.pages.print.typeNum}]
  ## images
  tt_content.slickslideshow {
    20 {
      1 {
        layout {
          default {
            element := appendString ( <br /><br /> )
          }
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
      }
      rendering {
        simple {
          caption.wrap = <p>|</p>
        }
      }
    }
  }
[global]

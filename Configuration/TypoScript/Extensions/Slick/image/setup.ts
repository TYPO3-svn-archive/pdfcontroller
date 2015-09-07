[globalVar = GP:type = {$plugin.pdfcontroller.pages.print.typeNum}]
  ## images
  tt_content.slickslideshow {
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
        # above-right
        1 = TEXT
        1 {
          value (
            <table cellspacing="0" cellpadding="0" style="width: 100%; text-align: left; margin: 0;">
              <tr>
                <td valign="top" align="right" style="text-align: right;">
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
        # above-left
        2 = TEXT
        2 {
          value (
            <table cellspacing="0" cellpadding="0" style="width: 100%; text-align: left; margin: 0;">
              <tr>
                <td valign="top" align="left" style="text-align: left;">
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
        # below-center
        8 = TEXT
        8 {
          value (
            <table cellspacing="0" cellpadding="0" style="width: 100%; text-align: left; margin: 0;">
              <tr>
                <td valign="bottom" align="center" style="text-align: center;">
                  ###IMAGES###
                </td>
              </tr>
              <tr>
                <td valign="bottom">
                  ###TEXT###
                </td>
              </tr>
            </table>
)
          override >
        }
        # below-right
        9 = TEXT
        9 {
          value (
            <table cellspacing="0" cellpadding="0" style="width: 100%; text-align: left; margin: 0;">
              <tr>
                <td valign="bottom" align="right" style="text-align: right;">
                  ###IMAGES###
                </td>
              </tr>
              <tr>
                <td valign="bottom">
                  ###TEXT###
                </td>
              </tr>
            </table>
)
          override >
        }
        # below-left
        10 = TEXT
        10 {
          value (
            <table cellspacing="0" cellpadding="0" style="width: 100%; text-align: left; margin: 0;">
              <tr>
                <td valign="bottom" align="left" style="text-align: left;">
                  ###IMAGES###
                </td>
              </tr>
              <tr>
                <td valign="bottom">
                  ###TEXT###
                </td>
              </tr>
            </table>
)
          override >
        }
        # intext-right
        17 = TEXT
        17 {
          value (
            <table cellspacing="0" cellpadding="0" style="width: 100%; text-align: left; margin: 0;">
              <tr>
                <td valign="top" align="right" style="text-align: right;">
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
        # intext-left
        18 = TEXT
        18 {
          value (
            <table cellspacing="0" cellpadding="0" style="width: 100%; text-align: left; margin: 0;">
              <tr>
                <td valign="top" align="left" style="text-align: left;">
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

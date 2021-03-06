[globalVar = GP:type = {$plugin.pdfcontroller.pages.print.typeNum}]
  ## images
  tt_content.image {
    20 {
      1 {
        layout {
          default {
            // #i0034, 150915, dwildt, 1-
            //element := appendString ( <br /> )
          }
        }
      }
      layout {
        # above-center
        default {
          value (
            <table cellspacing="0" cellpadding="0" class="columns01 align-center">
              <tr>
                <td class="align-center">
                  ###IMAGES###
                </td>
              </tr>
              <tr>
                <td class="align-center">
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
            <table cellspacing="0" cellpadding="0" class="columns01 align-right">
              <tr>
                <td>
                  ###IMAGES###
                </td>
              </tr>
              <tr>
                <td>
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
            <table cellspacing="0" cellpadding="0" class="columns01">
              <tr>
                <td>
                  ###IMAGES###
                </td>
              </tr>
              <tr>
                <td>
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
            <table cellspacing="0" cellpadding="0" class="columns01">
              <tr>
                <td>
                  ###TEXT###
                </td>
              </tr>
              <tr>
                <td>
                  ###IMAGES###
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
            <table cellspacing="0" cellpadding="0" class="columns01">
              <tr>
                <td>
                  ###TEXT###
                </td>
              </tr>
              <tr>
                <td>
                  ###IMAGES###
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
            <table cellspacing="0" cellpadding="0" class="columns01">
              <tr>
                <td>
                  ###TEXT###
                </td>
              </tr>
              <tr>
                <td>
                  ###IMAGES###
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
            <table cellspacing="0" cellpadding="0" class="columns02">
              <tr>
                <td>
                  ###TEXT###
                </td>
                <td class="space">
                  &nbsp;
                </td>
                <td>
                  ###IMAGES###
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
            <table cellspacing="0" cellpadding="0" class="columns02">
              <tr>
                <td>
                  ###IMAGES###
                </td>
                <td class="space">
                  &nbsp;
                </td>
                <td>
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
            <table cellspacing="0" cellpadding="0" class="columns02">
              <tr>
                <td>
                  ###TEXT###
                </td>
                <td class="space">
                  &nbsp;
                </td>
                <td>
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
            <table cellspacing="0" cellpadding="0" class="columns02">
              <tr>
                <td>
                  ###IMAGES###
                </td>
                <td class="space">
                  &nbsp;
                </td>
                <td>
                  ###TEXT###
                </td>
              </tr>
            </table>
)
          override >
        }
      }
      renderMethod = simple
      rendering {
        simple {
          imageStdWrapNoWidth.wrap = |
          imageStdWrap.dataWrap = |
          // #i0034, 150915, dwildt, 1-/+
          //caption.wrap = <p>|<br /><br /></p>
          caption.wrap = </td></tr><tr><td>|
        }
      }
    }
  }
[global]
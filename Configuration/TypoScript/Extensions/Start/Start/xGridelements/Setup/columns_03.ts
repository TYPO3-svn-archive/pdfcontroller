start {
    // for proper comments only
  gridelements {
  }
    // 3 columns
  gridelements =
  gridelements {
    3 < lib.gridelements.defaultGridSetup
      // for proper comments only
    3 {
    }
      // 3 columns: wrap, columns
    3 =
    3 {
      wrap {
        stdWrap {
          cObject = COA
          cObject {
              // <div ...
            10 = TEXT
            10 {
              value = <div class="row
            }
              // class
            20 = TEXT
            20 {
              field       = flexform_class
              noTrimWrap  = | ||
              required    = 1
            }
              // ... /div>
            30 = TEXT
            30 {
              value = ">|</div>
            }
          }
        }
      }
      columns {
        default {
          wrap {
            stdWrap {
              cObject = COA
              cObject {
                  // class
                10 = TEXT
                10 {
                  field       = flexform_class
                  noTrimWrap  = ||-1 |
                  required    = 1
                }
                  // small
                20 = TEXT
                20 {
                  field = flexform_columnssmall
                  listNum = 1
                  listNum {
                    splitChar = /
                  }
                  noTrimWrap = |small-| |
                  required = 1
                }
                  // medium
                30 = TEXT
                30 {
                  field = flexform_columnsmedium
                  listNum = 1
                  listNum {
                    splitChar = /
                  }
                  noTrimWrap = |medium-| |
                  required = 1
                }
                  // large
                40 = TEXT
                40 {
                  field = flexform_columnslarge
                  listNum = 1
                  listNum {
                    splitChar = /
                  }
                  noTrimWrap = |large-| |
                  required = 1
                }
              }
            }
            noTrimWrap = |<div class="columns |">|
          }
          wrap2 = |</div>
        }
        1 < .default
        1 {
          wrap {
            stdWrap {
              cObject {
                10 {
                  noTrimWrap  = ||-0 |
                }
                20 {
                  listNum = 0
                }
                30 {
                  listNum = 0
                }
                40 {
                  listNum = 0
                }
                  // first
                99 = TEXT
                99 {
                  value       = first
                  noTrimWrap  = || |
                }
              }
            }
          }
        }
        2 < .default
        2 {
          wrap {
            stdWrap {
              cObject {
                10 {
                  noTrimWrap  = ||-2 |
                }
                20 {
                  listNum = 2
                }
                30 {
                  listNum = 2
                }
                40 {
                  listNum = 2
                }
                  // last
                99 = TEXT
                99 {
                  value       = last
                  noTrimWrap  = || |
                }
              }
            }
          }
        }
      }
    }
  }
}

tt_content.gridelements_pi1.20.10.setup.3 < start.gridelements.3
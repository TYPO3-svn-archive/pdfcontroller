start {
    // for proper comments only
  gridelements {
  }
    // 6 columns
  gridelements =
  gridelements {
    6 < lib.gridelements.defaultGridSetup
      // for proper comments only
    6 {
    }
      // 6 columns: wrap, columns
    6 =
    6 {
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
                  noTrimWrap  = ||-0 |
                  required    = 1
                }
                  // small
                20 = TEXT
                20 {
                  field = flexform_columnssmall
                  listNum = 0
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
                  listNum = 0
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
                  listNum = 0
                  listNum {
                    splitChar = /
                  }
                  noTrimWrap = |large-| |
                  required = 1
                }
                  // first
                99 = TEXT
                99 {
                  value       = first
                  noTrimWrap  = || |
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
                  noTrimWrap  = ||-1 |
                }
                20 {
                  listNum = 1
                }
                30 {
                  listNum = 1
                }
                40 {
                  listNum = 1
                }
                99 >
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
                99 >
              }
            }
          }
        }
        3 < .default
        3 {
          wrap {
            stdWrap {
              cObject {
                10 {
                  noTrimWrap  = ||-3 |
                }
                20 {
                  listNum = 3
                }
                30 {
                  listNum = 3
                }
                40 {
                  listNum = 3
                }
                99 >
              }
            }
          }
        }
        4 < .default
        4 {
          wrap {
            stdWrap {
              cObject {
                10 {
                  noTrimWrap  = ||-4 |
                }
                20 {
                  listNum = 4
                }
                30 {
                  listNum = 4
                }
                40 {
                  listNum = 4
                }
                99 >
              }
            }
          }
        }
        5 < .default
        5 {
          wrap {
            stdWrap {
              cObject {
                10 {
                  noTrimWrap  = ||-5 |
                }
                20 {
                  listNum = 5
                }
                30 {
                  listNum = 5
                }
                40 {
                  listNum = 5
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

tt_content.gridelements_pi1.20.10.setup.6 < start.gridelements.6
lib.gridelements {
	10 < .defaultGridSetup
	10 {
		dataWrap = <{field:flexform_sectiontype} {field:flexform_dataoptions}>
		wrap2 = |</div>
		columns {
		  default {
        renderObj {
          20 >
          20 = CASE
          20 {
            key.dataWrap = {field:CType}_{field:parentgrid_flexform_section}
            text_0 =< tt_content.text
            text_1 = COA
            text_1 {
              wrap = <section> | </section>
              10 = TEXT
              10 {
                dataWrap = <p class="title" data-section-title><a href="#panel{COBJ:parentRecordNumber}">{field:header}</a></p>
              }
              20 = COA
              20 {
                wrap = <div class="content" data-section-content>|</div>
                20 =< tt_content.text.20
              }
            }
            textpic_0 =< tt_content.textpic
            textpic_1 < .text_1
            textpic_1 {
              20 {
                20 < tt_content.textpic.20
                20 {
                  text {
                    10 >
                  }
                }
              }
            }
  				}
				}
			}
		}
	}
}
tt_content.gridelements_pi1.20.10.setup.10 < lib.gridelements.10

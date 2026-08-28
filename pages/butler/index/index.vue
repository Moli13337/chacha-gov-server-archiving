<template>
	<div class="project_push-container">
		<el-tabs
			v-model="activeName"
			@tab-click="handleClick"
		>
			<el-tab-pane
				label="申报项目推荐"
				name="first"
			>
				<ProjectRecommend  ref="projectRecomment"/>
			</el-tab-pane>
			<el-tab-pane
				label="行业动态推荐"
				name="second"
			>
				<IndustrNews ref="industryInfo"/>
			</el-tab-pane>
			<el-tab-pane
				label="会议通知推荐"
				name="third"
			>
				<MeetingNotice ref="meetingNotice"/>
			</el-tab-pane>
		</el-tabs>
	</div>
</template>
<script>

import ProjectRecommend from '@/components/butler/project_recommend.vue';
import IndustrNews from '@/components/butler/industry_news.vue';
import MeetingNotice from '@/components/butler/meeting_notice.vue';
export default {
	components: {
		ProjectRecommend,
		IndustrNews,
		MeetingNotice
	},
	data() {
		return {
			activeName: 'first',
			keyword: '',
		};
	},
	methods: {
		loadTabData() {
			switch (this.activeName) {
				case 'first':
					this.$refs.projectRecomment.featchProjectRecommentList(1, 10);
					this.$refs.projectRecomment.featchProjecTrumpetList(1, 4);
					break;
				case 'second':
					this.$refs.industryInfo.featchIndustryInfoList(1, 10);
					this.$refs.industryInfo.featchIndustryTrumpetList(1, 4);
					break;
				case 'third':
					this.$refs.meetingNotice.featchMeetingNoticeList(1, 10);
					this.$refs.meetingNotice.featchMeetingTrumpetList(1, 4);
					break;
				default:
					break;
			}
		},
		handleClick() {
			// let name = event.name;
			this.loadTabData();
		},
	},
	watch: {
		$route() {
			this.loadTabData();
		}
	}
};
</script>
<style lang="less" scoped>
@import '~assets/css/common_avairail.less';
.project_push-container {
  .explain {
      display: flex;
      flex-direction: row;
      align-items: center;
      padding: 18px 32px;
      background: @applyItemBgColor;
      border: 1px solid #bcd5e9;

      .right {
        font-size:14px;
        font-weight:400;
        color:rgba(0,81,146,1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        .icon_tips {
          width: 39px;
          height: 39px;
          margin-right: 20px;
        }
      }
    }
    .title-box {
      width:100%;
      height:36px;
      background:rgba(0,81,146,1);
      margin: 20px 0;
      font-size:16px;
      font-weight:bold;
      color:rgba(255,255,255,1);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 20px;
      .icon_box {
        display: flex;
        align-items: center;
      }
      .icon_tip {
        width: 17px;
        height: 14px;
        margin-right: 10px;
      }
      .more {
        cursor: pointer;
      }
    }
    .item-box {
        width:100%;
        // height:110px;
        margin-bottom: 20px;
        background:rgba(255,255,255,1);
        border:1px solid rgba(220,223,230,1);
        padding: 20px;
        .item-title-box {
          display: flex;
          justify-content: space-between;
          .time {
            font-size:14px;
            color:rgba(203,203,203,1);
          }
        }
      .item-title {
        font-size:16px;
        font-weight:bold;
        cursor: pointer;
        }
        .item-text {
          font-size:14px;
          padding-top: 10px;
        }
      .square {
        display: inline-block;
        width:10px;
        height:10px;
        border:2px solid rgba(0,81,146,1);
        margin-right: 10px;
      }
      .new {
        color:rgba(255,70,70,1);
      }
      .declara-tip {
        width:80px;
        height:25px;
        background:rgba(204,0,0,1);
        color:rgba(255,255,255,1);
        font-size:14px;
        padding: 3px 20px;
      }
      .declara-gray {
         background:rgb(139, 139, 139);
      }
    }
    .tip-box {
      font-size:14px;
      display: flex;
      justify-content: flex-start;
      padding-top: 10px;
      .tip {
        color: #CBCBCB;
        margin-right: 50px;
      }
      .tip-content {
        color: #818181;
      }
    }
    .empty-box{
      height: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding-top: 100px;
      .text {
        padding: 20px;
      }
    }
}

</style>

<template>
	<div class="meeting-notice-container">
		<div class="explain">
			<img class="small">
			<div class="right">
				<div>
					<img
						class="icon_tips"
						src="~assets/images/butler/icon_Tips.png"
					>
				</div>
				<p>	企业管家实时为你推送最新会议通知信息<br/>
					用户在关注的行业处，选择企业关注的信息标签之后，系统将会推荐企业关注的会议通知</p>
			</div>
		</div>
		<div>
			<div class="title-box">
				<div class="icon_box"><img
					class="icon_tip icon_horn"
					src="~assets/images/butler/icon_horn.png"
				>小喇叭</div>
				<div
					class="more"
					@click="handleMore"
				>查看历史推送 >></div>
			</div>
			<div v-loading="trumpetListLoading">
				<empty
					v-if="!trumpetList || !trumpetList.length"
					tip="小喇叭暂无数据"
				/>
				<div
					v-else
					class="item-box"
					v-for="(item, index) in  trumpetList"
					:key="index"
				>
					<div class="item-title-box">
						<p
							class="item-title"
							@click="handleTodeclareDetail(item.id)"
						><span class="square"></span>{{item.obj_type_name}} <span
							v-if="item.is_new == 1"
							class="new"
						>new</span></p>
						<p class="time">{{item.created_at | formatDate('YYYY-MM-DD HH:mm:ss')}} </p>
					</div>
					<p class="item-text">{{item.content || '无'}}</p>
				</div>
			</div>
			<div class="title-box">
				<div class="icon_box"><img
					class="icon_tip icon_horn"
					src="~assets/images/butler/icon_recommend.png"
				>会议通知推荐</div>
			</div>
			<div v-loading="Listloading">
				<meetingNoticeList :list="meetingNoticeList"/>
			</div>
		</div>
		<pagination
			:pagination="pagination"
			@onPageChange="onPageChange"
		/>
	</div>
</template>
<script>
import pagination from '@/components/pagination.vue';
import empty from '@/components/empty.vue';
import meetingNoticeList from '../../components/butler/meeting_notice_list';
import {
	PROJECT_PUSH_TRUMPET,
	INFORMATION_LIST
} from '@/utils/urls.js';
export default {
	components: {
		pagination,
		empty,
		meetingNoticeList
	},
	data() {
		return {
			Listloading: false,
			trumpetListLoading: false,
			// 会议通知列表
			meetingNoticeList: [],
			// 小喇叭会议通知数据
			trumpetList: [],
			// 分页
			pagination: {
				total: 0,
				pageCount: 1,
				pageSize: 10,
				totalPage: 0,
			}
		};
	},
	mounted() {
		// 进入页面获取数据
		this.featchMeetingTrumpetList(1, 4);
		this.featchMeetingNoticeList(1, 10);
	},
	methods: {

		// 获取会议通知
		featchMeetingNoticeList(pageCount, pageSize) {
			let meetingParams = {
				keyword: this.$route.query.keyword,
				type: 15,
				per_page: pageSize || this.pagination.pageSize,
				page: pageCount || this.pagination.pageCount,
			};

			this.Listloading = true;
			this.$axios.get(INFORMATION_LIST, {params: meetingParams}).then(res => {
				this.Listloading = false;
				this.meetingNoticeList = res.data || [];
				this.pagination.total = res.total;
				this.pagination.pageCount = res.current_page;
				this.pagination.totalPage = res.total_page;
			}).catch(error => {
				console.log(error.message);
			});
		},

		// 获取会议通知小喇叭，获取4条
		featchMeetingTrumpetList(pageCount, pageSize) {
			let meetingParams = {
				obj_type: 15,
				per_page: pageSize || 4,
				page: pageCount || 1,
			};

			this.trumpetListLoading = true;
			this.$axios.get(PROJECT_PUSH_TRUMPET, {params: meetingParams}).then(res => {
				this.trumpetListLoading = false;
				this.trumpetList = res.data || [];
			}).catch(error => {
				console.log(error.message);
			});
		},

		// 查看更多会议通知小喇叭信息
		handleMore() {
			console.log();
			this.$router.push({
				name: 'butler-butler_components-message_list',
				query: {
					mode: 'meeting_notice'
				}
			});
		},

		// 页码变化触发获取会议通知
		onPageChange(pageCount) {
			this.featchMeetingNoticeList(pageCount);
		},

		// 查看详情
		handleTodeclareDetail(id) {
			this.$router.push({
				path: '/butler/butler_components/industry_news',
				query: {
					id,
					type: 'information'
				}
			});
		}

	},
};
</script>
<style lang="less" scoped>

@import '~assets/css/common_avairail.less';
.meeting-notice-container{
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

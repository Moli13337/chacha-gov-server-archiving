<template>
	<div class="industry_news_contianer">
		<div class="explain">
			<img class="small">
			<div class="right">
				<div>
					<img
						class="icon_tips"
						src="~assets/images/butler/icon_Tips.png"
					>
				</div>
				<p>	企业管家实时为你推送最新行业动态信息<br/>
					用户在关注的行业处，选择企业关注的信息标签之后，系统将会推荐企业关注的行业动态</p>
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
					<div
						class="item-text"
					>
						<div
							id="description"
							v-html="richTextToEllipsis(item.content, 150)"
						></div>
					</div>
				</div>
			</div>
			<div class="title-box">
				<div class="icon_box"><img
					class="icon_tip icon_horn"
					src="~assets/images/butler/icon_recommend.png"
				>动态行业推荐</div>
			</div>
			<div v-loading="Listloading">
				<!-- <empty v-if="industryInfoList && industryInfoList.length <= 0"/> -->
				<!-- <div
					v-else
					class="item-box"
					v-for="(item, index) in industryInfoList"
					:key="'industryInfo' + index"
				>
					<p
						class="item-title"
						@click="handleTodeclareDetail(item.id)"
					><span>【行业动态】{{item.title}}</span></p>
					<div class="item-text"><div v-html="item.content"></div>
					</div>
					<div class="tip-box">
						<p class="tip">来源：<span  class="tip-content">{{item.source_name}}</span></p>
						<p class="tip">发布时间：<span  class="tip-content">
							{{item.publish_time | formatDate('YYYY-MM-DD')}}
						</span></p>
					</div>
				</div> -->
				<industryNewsList :list="industryInfoList"/>
			</div>
		</div>
		<pagination
			:pagination="pagination"
			@onPageChange="onPageChange"
		/>
	</div>
</template>
<script>
import {
	PROJECT_PUSH_TRUMPET,
	INFORMATION_LIST
} from '@/utils/urls.js';
import pagination from '@/components/pagination.vue';
import empty from '@/components/empty.vue';
import industryNewsList from '@/components/butler/industry_news_list';
export default {
	components: {
		pagination,
		empty,
		industryNewsList
	},
	data() {
		return {
			Listloading: false,
			trumpetListLoading: false,
			industryInfoList: [],
			trumpetList: [],
			pagination: {
				total: 0,
				pageCount: 1,
				pageSize: 10,
				totalPage: 0,
			}
		};
	},
	mounted() {
		this.featchIndustryInfoList(1, 10);
		this.featchIndustryTrumpetList(1, 4);
	},
	methods: {
		// 获取行业动态信息
		featchIndustryInfoList(pageCount, pageSize) {
			let infoParams = {
				keyword: this.$route.query.keyword,
				type: 14,
				per_page: pageSize || this.pagination.pageSize,
				page: pageCount || this.pagination.pageCount,
			};

			this.Listloading = true;
			this.$axios.get(INFORMATION_LIST, {params: infoParams}).then(res => {
				this.Listloading = false;
				this.industryInfoList = res.data || [];
				this.pagination.total = res.current_page;
				this.pagination.pageCount = res.current_page;
				this.pagination.totalPage = res.current_page;
			}).catch(error => {
				console.log(error.message);
			});
		},

		// 获取项目推荐小喇叭
		featchIndustryTrumpetList(pageCount, pageSize) {
			let params = {
				page: pageCount || 1,
				per_page: pageSize || 4,
				obj_type: 14,
			};

			this.trumpetListLoading = true;
			this.$axios.get(PROJECT_PUSH_TRUMPET, {params: params}).then(res => {
				this.trumpetListLoading = false;
				this.trumpetList = res.data || [];
			}).catch(error => {
				console.log(error.message);
			});
		},
		handleMore() {
			console.log();
			this.$router.push({
				name: 'butler-butler_components-message_list',
				query: {
					mode: 'industry_news'
				}
			});
		},
		onPageChange(pageCount) {
			this.featchIndustryInfoList(pageCount);
		},
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
.industry_news_contianer{
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
          .el-table__body {
            border: 1px solid @borderLine !important;
          }
          #description {
            table {
                border-collapse: collapse;
              tr,th,td {
                border: 1px solid @borderLine;
              }
            }
          }

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

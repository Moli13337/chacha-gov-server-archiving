<template>
	<div class="support-info-container">
		<div class="title-box">
			<div>获得支持总览</div>
		</div>
		<div class="explain">
			<img class="small">
			<div class="right">
				企业可以在本模块查看政策申报的项目中获得支持的项目信息。
			</div>
		</div>
		<div class="support-data-box">
			<empty v-if="!supportOverviewList || !supportOverviewList.length"/>
			<div class="support-data">
				<div
					class="support-year-data"
					v-for="(item, index) in supportOverviewList"
					:key="index"
				>
					<p class="year">{{item.year}}年</p>
					<div class="data-box">
						<div class="data-item">
							<p
								class="number"
								:class="{samllSize: item.number.length >= 4}"
							>{{item.number || 0}}</p>
							<p class="tip">申报项目(个)</p>
						</div>
						<div class="data-item">
							<p
								class="number"
								:class="{samllSize: item.money.length >= 4}"
							>{{item.money || 0}}</p>
							<p class="tip">申报金额(万元)</p>
						</div>
						<div class="data-item">
							<p
								class="number"
								:class="{samllSize: item.accept_money.length >= 4}"
							>{{item.accept_money || 0}} </p>
							<p class="tip">受理金额(万元)</p>
						</div>
						<div class="data-item">
							<p
								class="number"
								:class="{samllSize: item.support_number.length >= 4}"
							>{{item.support_number || 0}}</p>
							<p class="tip">获得支持项目(个)</p>
						</div>
						<div class="data-item">
							<p
								class="number"
								:class="{samllSize: item.support_money.length >= 4}"
							>{{item.support_money || 0}}</p>
							<p class="tip">兑现金额(万元)</p>
						</div>
					</div>
					<el-divider v-if="supportOverviewList.length != 1"></el-divider>
				</div>
			</div>
			<p
				class="more"
				@click="handleMoreRview"
			>{{tip}}</p>
		</div>
		<div class="title-box">
			<div>获得支持的政策申报</div>
		</div>
		<div 	v-loading="loading">
			<empty v-if="!applySupportList || !applySupportList.length"/>
			<div
				v-else
				class="declare-item-box"
				v-for="(item, index) in applySupportList"
				:key="index"
			>
				<div class="image-box">
					<img
						class="image"
						src="~assets/images/butler/pic_declare.png"
					>
				</div>
				<div class="item-title-box">
					<p
						class="item-title"
						@click="handleSupportDetail(item.id)"
					>申报项目：{{item.policy_name}}</p>
					<p class="time"><span class="bold">申报金额：</span><span class="number">{{item.apply_money}}万元</span><span class="bold">获得支持： </span><span class="number">{{item.support_content}}万元</span></p>
					<p class="time"><span class="bold">拨款时间：</span><span class="number">{{item.allocation_time | formatDate('YYYY-MM-DD')}}</span></p>
				</div>
			</div>
			<pagination
				:pagination="pagination"
				@onPageChange="onPageChange"
			/>
		</div>
	</div>
</template>
<script>
import pagination from '@/components/pagination.vue';
import empty from '@/components/empty.vue';
import {
	APPLY_SUPPORT_LIST,
	SUPPORT_OVERVIEW
} from '@/utils/urls.js';
export default {
	components: {
		pagination,
		empty
	},
	data() {
		return {
			loading: false,
			tip: '查看更多'
		};
	},

	async asyncData({$axios}) {
		let params = {
			page: 1,
			per_page: 10
		};

		return Promise.all([
			$axios.get(APPLY_SUPPORT_LIST, {params: params}),
			$axios.get(SUPPORT_OVERVIEW)
		])
			.then(([applySupportList, resultverviewList]) => ({
				applySupportList: applySupportList.data || [],
				allOverviewList: resultverviewList || [],
				supportOverviewList: resultverviewList.slice(0, 1),
				pagination: {
					total: applySupportList.total,
					pageCount: applySupportList.current_page,
					pageSize: 10,
					totalPage: applySupportList.total_page,
				}
			}))
			.catch(e => {
				console.log(e);
			});
	},
	methods: {
		feathApplySupportList() {
			let params = {
				page: this.pagination.pageCount,
				per_page: this.pagination.pageSize
			};

			if (this.$route.query.keyword) {
				params.keyword = this.$route.query.keyword;
			}
			this.loading = true;
			this.$axios.get(APPLY_SUPPORT_LIST, {params}).then(res => {
				this.loading = false;
				this.applySupportList = res.data || [];
				this.pagination.total = res.total;
				this.pagination.pageCount = res.current_page;
				this.pagination.totalPage = res.total_page;
			}).catch(error => {
				console.log(error);
			});
		},
		handleMore() {
			console.log();
			this.$router.push({
				name: 'butler-index-project_push-message_list'
			});
		},

		// 展开更多
		handleMoreRview() {
			if (this.tip == '查看更多') {
				this.supportOverviewList = this.allOverviewList;
				this.tip = '收起';
			} else if (this.tip == '收起') {
				this.supportOverviewList = this.allOverviewList.slice(0, 1);
				this.tip = '查看更多';
			}
		},

		handleIndustryNew() {
			this.$router.push({
				name: 'butler-butler_components-industry_news'
			});
		},
		onPageChange(pageCount) {
			this.feathApplySupportList(pageCount);
		},
		handleSupportDetail(id) {
			if (!id) {
				this.$message.error('当前申报信息不存在');
				return;
			}
			let routeData =	this.$router.resolve({path: '/notice/appropriation', query: {id: id}});

			window.open(routeData.href, '_blank');
		}
	},
	watch: {
		$route() {
			this.feathApplySupportList();
		}
	}
};
</script>
<style lang="less" scoped>
@import '~assets/css/common_avairail.less';
.support-info-container {
  .explain {
      display: flex;
      flex-direction: row;
      align-items: center;
      padding: 18px 32px;
      background: @applyItemBgColor;
      border: 1px solid #bcd5e9;

      .right {
        font-size:14px;
        font-family:Microsoft YaHei;
        font-weight:400;
        color:rgba(0,81,146,1);
      }
    }
    .support-data-box {
      border:1px solid rgba(220,223,230,1);
      margin: 20px 0;
    }
    .support-data {
      padding: 0 20px;
      background:rgba(255,255,255,1);
      border-radius:4px;

      .year {
        font-size:16px;
        padding: 20px 0 0 0;
      }
      .support-year-data {
        height: 156px;
      }
      .data-box {
        display: flex;
        justify-content: center;
        .number {
          font-size:30px;
          color:rgba(39,170,61,1);
          font-weight: 500;
          min-height: 45px;
          line-height: 45px;
        }
        .samllSize {
          font-size: 16px;
        }
        .tip {
          font-size:18px;
          color:rgba(129,129,129,1);
        }
        .data-item {
          text-align: center;
          padding: 20px;
        }
      }
      .el-divider--horizontal {
        margin: 0;
        background-color: rgb(244, 245, 248);
      }

    }
    .more {
      width: 100%;
      height: 40px;
      background:rgba(249,251,252,1);
      opacity:0.5;
      border-radius:0px 0px 4px 4px;
      text-align: center;
      line-height: 38px;
      border-top: 1px solid rgb(244, 245, 248);;
      }
      .search-box {
         width: 400px;
         margin-bottom: 20px;
        .el-input .el-input__inner {
          border-radius: 0 !important;
        }
        // .search-btn {
        //   height: 38px;
        //   padding: 0 20px;
        //   background: #005192;
        //   line-height: 40px;
        //   color: #ffffff;
        //   font-size: 16px;
        //   border-radius: 0;
        // }
      }
      .declare-item-box {
        display: flex;
        justify-content: space-between;
        padding: 20px;
        background:rgba(255,255,255,1);
        box-shadow:0px 0px 5px rgba(0,0,0,0.05);
        border:1px solid rgba(0,81,146,1);
        border-radius:4px;
        margin-bottom: 20px;
        .image-box {
          width: 105px;
          height: 105px;
          margin-right: 20px;
        }
        .image {
          width: 105px;
          height: 105px;
        }
        .item-title-box {
          flex: 1;
        }
        .item-title {
          font-size:16px;
          font-weight:bold;
          padding: 10px 0;
          &:hover {
            color:rgba(0,81,146,1);
          }
        }

        .time {
          color: #818181;
          padding-bottom: 10px;
        }
        .number {
          margin-right: 50px;
        }
        .bold {
          font-weight: 500;
          color: #3B3B3B;
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
          }
        }
      .item-title {
        font-size:16px;
        font-weight:bold;
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
}

</style>

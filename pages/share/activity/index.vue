<template>
	<div class="activity-list">
		<div class="breadcrumb-row">
			<el-divider direction="vertical"></el-divider>
			当前位置：
			<el-breadcrumb separator-class="el-icon-arrow-right">
				<el-breadcrumb-item>
					<nuxt-link to="/">首页</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<nuxt-link to="/share">共享空间</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<div>活动列表</div>
				</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<!-- 搜索框 -->
		<div
			class="search-container"
		>
			<div
				class="search-box"
			>
				<div class="input-box">
					<el-input
						class="search"
						placeholder="请输入活动名称"
						v-model="filters.keyword"
					>
						<el-button
							slot="append"
							icon="el-icon-search"
							@click="handleSearch"
						/>
					</el-input>
				</div>
			</div>
		</div>
		<ShareFiflters
			:filters="filters"
			@onChange="handleFiltersChange"
			:agentTotal="agentTotal"
		/>
		<p>为您找到以下活动信息:</p>
		<ActivityList
			:isShowShadow="true"
			:isShowDivider="false"
			:activityList="activityList"
			@onRegistChange="handleRegistChange"
			v-loading="loading"
		/>
		<Pagination
			:pagination="pagination"
			@onPageChange="onPageChange"
		/>
	</div>
</template>
<script>
import {
	ACTIVITY_LIST
} from '@/utils/urls.js';
import {
	districtCodeMap
} from '@/utils/district';
import ShareFiflters from '@/components/share/sharet-fiflters.vue';
import ActivityList from '@/components/share/activity_list.vue';
import Pagination from '@/components/pagination';

export default {
	components: {
		ShareFiflters,
		ActivityList,
		Pagination
	},
	data() {
		return {
			loading: false,
			filters: {
				selectTime: '',
				status: '',
				apply_status: ''
			},
		};
	},

	// 初始化活动
	async asyncData({$axios, query}) {
		let requestPramas = {
			page: 1,
			per_page: 10,
		};

		if (query.keyword) {
			requestPramas.keyword = query.keyword;
		}

		if (query.status) {
			requestPramas.status = query.status;
		}
		return Promise.all([
			$axios.get(ACTIVITY_LIST, {params: requestPramas}),
		]).then(([activityList]) => ({

			activityList: activityList.data || [],
			agentTotal: activityList.total || 0,
			pagination: {
				total: activityList.total,
				pageCount: 1,
				pageSize: 10
			},
			filters: {keyword: query.keyword}
		})).catch(error => {
			console.log(error);
		});
	},
	computed: {
		// 后端参数处理
		filterParams() {
			let filterParams = {};

			// 报名时间
			if (this.filters.selectTime && this.filters.selectTime.length > 0) {
				filterParams.selectTime = this.filters.selectTime && this.filters.selectTime[0];
			}

			// 报名时间
			if (this.filters.selectTime && this.filters.selectTime.length > 0) {
				filterParams.selectTime = this.filters.selectTime && this.filters.selectTime[0];
			}

			// 报名状态
			if (this.filters.status && this.filters.status.length > 0) {
				filterParams.status = this.filters.status && this.filters.status[0];
			}

			// 活动报名状态
			if (this.filters.apply_status && this.filters.apply_status.length > 0) {
				filterParams.apply_status = this.filters.apply_status && this.filters.apply_status[0];
			}

			// 适用地区
			if (this.filters.suitableRegion || this.filters.suitableRegion == 0) {
				let mapItem = districtCodeMap[this.filters.suitableRegion];

				if (mapItem) {
					filterParams.province_code = mapItem.province_code;
					filterParams.city_code = mapItem.city_code;
					filterParams.district_code = mapItem.district_code;
				}
			}

			// 关键词
			if (this.filters.keyword) {
				filterParams.keyword = this.filters.keyword;
			}
			return filterParams;
		}
	},
	methods: {
		// 过滤参数变化
		handleFiltersChange(filters) {
			this.filters = filters;
			this.fetchActivityList(1, 10);
		},
		// 获取机构列表
		fetchActivityList(pageId, pageSize) {
			this.loading = true;
			let params = {
				page: pageId || this.pagination.pageCount,
				per_page: pageSize || this.pagination.pageSize,
				...this.filterParams
			};

			this.$axios.get(ACTIVITY_LIST, {params: params}).then(res => {
				this.loading = false;
				this.activityList = res.data || [],
				this.pagination.total = res.total;
				this.agentTotal = res.total;
			}).catch(error => {
				console.log(error.message);
				this.loading = false;
			});
		},
		onPageChange(val) {
			this.fetchActivityList(val);
			console.log('filters', this.filters);
		},
		// 搜索
		handleSearch() {
			this.fetchActivityList();
		},
		handleRegistChange(id) {
			console.log('handleRegistChange', id);

			this.activityList = this.activityList.map(it => {
				if (it.id === id) {
					return {
						...it,
						apply_count: 1
					};
				}
				return it;
			});
		}
	},
	watch: {
		$route() {
			this.fetchActivityList(1, 10);
		}
	}
};
</script>
<style lang="less">
@import "~assets/css/common_avairail";
.activity-list {
 // 搜索样式
  .search-container {
    margin-bottom: 20px;
    .search-box {
      padding: 20px 0;
      width: 1162px;
      margin: 0 auto;
      box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.05);
      border: 1px solid @backGroundColor;
      background: @backGroundColor;
      .input-box {
        width: 550px;
        margin: 0 auto;
        .search {
          text-align: center;
          .el-input__inner {
            background: none;
            height: 49px;
            border: 1px solid @primaryColor;
            font-family: MicrosoftYaHei-Bold;
            padding-right: 0;
            position: relative;
          }
          .el-button {
            width: 65px;
            font-size: 24px;
            color: @backGroundColor;
            background: @primaryColor;
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
          }
        }
      }
      .search-options {
        width: 550px;
        margin: 0 auto;
        color: #818181;
        font-weight: 500;
        padding: 10px 0;
        font-size: 14px;
        .search-item {
          font-family: Microsoft YaHei;
          font-weight: 400;
          color: @textColor;
          cursor: pointer;
          padding-left: 5px;
        }
        .search-item:hover {
            color: @primaryColor;
          }
        .search-item:active {
            color: @primaryColor;
          }
      }
      .el-input-group__append {
        border: 1px solid  @primaryColor;
        background:  @primaryColor;
      }
    }
    .search-box-bg {
      background: url('~assets//images/search-agent.png');
      background-size: 100% 100%;
    }
  }
}

</style>


<template>
	<div class="notice-container">
		<div class="content-contanier">
			<el-form
				:inline="true"
				:model="filters"
				class="filters-option"
			>
				<el-form-item label="文件类型">
					<el-select
						placeholder="请选择"
						v-model="filters.fileType"
					>
						<el-option
							v-for="(item, index) in fileTypeOptions"
							:key="index"
							:value="item.value"
							:label="item.label"
						></el-option>
					</el-select>
				</el-form-item>
				<el-form-item label="适用地区">
					<el-select
						placeholder="请选择"
						v-model="filters.suitableRegion"
					>
						<el-option
							v-for="(item, index) in suitableRegionOptions"
							:key="index"
							:label="item.district_name"
							:value="item.district_code"
						/>
					</el-select>
				</el-form-item>
			</el-form>
			<div class="result-list-container">
				<p class="result-list-tip">为你找到以下公示公告信息</p>
				<notice-list
					:loading="loading"
					:list="noticeList"
					:pagination="pagination"
					@onPageChange="handlePageChange"
				/>
			</div>
		</div>
	</div>
</template>

<script>
import NoticeList from '@/components/notice/notice-list';
import {
	QUERY_NOTICE_LIST
} from '@/utils/urls';
import {
	districtCodeMap
} from '@/utils/district';

export default {
	components: {
		NoticeList,
	},
	data() {
		return {
			filters: {
				suitableRegion: 510115000000
			},
			loading: false,
			noticeList: [],
			pagination: {
				total: 0,
				pageId: 1,
				pageCount: 0,
				pageSize: 10
			},
			fileTypeOptions: [
				{
					value: 4,
					label: '申报公示公告',
				},
				{
					value: 7,
					label: '活动公示公告',
				},
				{
					value: 10,
					label: '拨款公示公告'
				},
			],
			suitableRegionOptions: [
				{
					district_code: 510115000000,
					district_name: '成都市温江区'
				},
				{
					district_code: 510100000000,
					district_name: '四川省成都市'
				},
				{
					district_code: 510000000000,
					district_name: '四川省'
				}
			]
		};
	},
	// 进入页面请求公告列表
	async asyncData({query, $axios}) {
		let params = {
			page: 1,
			per_page: 10,
			province_code: '510000000000',
			city_code: '510100000000',
			district_code: '510115000000'
		};

		// 搜索关键字
		if (query.keyword) {
			params.keyword = query.keyword;
		}
		const result = await $axios.get(QUERY_NOTICE_LIST, {params});
		const data = result || {};

		return {
			noticeList: data.data,
			pagination: {
				total: data.total,
				pageId: 1,
				pageCount: data.total_page,
				pageSize: 10
			}
		};
	},
	computed: {
		// 后端需要的过滤参数
		filterParams() {
			let filterParams = {};

			// 文件类型
			if (this.filters.fileType) {
				filterParams.obj_type = this.filters.fileType;
			}
			// 适用地区
			if (this.filters.suitableRegion) {
				let mapItem = districtCodeMap[this.filters.suitableRegion];

				if (mapItem) {
					filterParams.province_code = mapItem.province_code;
					filterParams.city_code = mapItem.city_code;
					filterParams.district_code = mapItem.district_code;
				}
			}
			return filterParams;
		}
	},
	methods: {
		// 根据过滤参数请求公告列表
		fetchNoticeList(pageId, pageSize) {
			if (this.$route.query.keyword) {
				this.$bus.emit('changeSearchContent', this.$route.query.keyword);
			}
			let params = {
				...this.filterParams,
				page: pageId || this.pagination.pageId,
				per_page: pageSize || this.pagination.pageSize,
			};

			// 添加搜索关键字
			if (this.$route.query && this.$route.query.keyword) {
				params.keyword = this.$route.query.keyword;
			}

			this.loading = true;
			this.$axios.get(QUERY_NOTICE_LIST, {params})
				.then((data = {}) => {
					this.loading = false;
					this.noticeList = data.data;
					this.pagination = {
						...this.pagination,
						total: data.total,
						pageId: data.current_page,
						pageCount: data.total_page
					};
				})
				.catch(error => {
					console.log(error);
					this.loading = false;
					this.$message.error('获取数据失败，请稍后重试');
				});
		},
		// 触发分页
		handlePageChange(pageId) {
			this.fetchNoticeList(pageId);
		}
	},
	watch: {
		filters: {
			deep: true,
			handler() {
				this.fetchNoticeList(1);
			}
		},
		// 路由变化后触发搜索
		$route() {
			this.fetchNoticeList(1);
		},
	},

};
</script>

<style lang="less">
@import "~assets/css/common_avairail.less";
.notice-container {
  margin-top: 20px;
  .filters-option {
    height: 78px;
    padding: 16px;
    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.05);
    background: @backGroundColor;
    .el-form-item {
      margin-right: 40px;
    }
    .el-input__inner {
      width: 232px;
      border-radius: 0;
    }
  }
  .result-list-container {
    margin-top: 32px;
  }
}
</style>

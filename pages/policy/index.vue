<template>
	<div class="policy-container">
		<!-- 过滤器 -->
		<policy-filters
			:filters="filters"
			:industryOptions="industryOptions"
			@onChange="handleFiltersChange"
		/>
		<!-- 查询列表 -->
		<div class="result-list-container">
			<p class="result-list-tip">为你找到以下政策信息</p>
			<policy-list
				:loading="loading"
				:list="policyList"
				:pagination="pagination"
				@onPageChange="handlePageChange"
			/>
		</div>
	</div>
</template>

<script>
import {
	FETCH_POLICY_LIST,
	FETCH_FIRST_INDUSTRY_LIST
} from '@/utils/urls';
import PolicyFilters from '@/components/policy/policy-filters';
import PolicyList from '@/components/policy/policy-list';
import {
	districtCodeMap
} from '@/utils/district';
export default {
	components: {
		PolicyFilters,
		PolicyList,
	},
	data() {
		return {
			filters: {
				suitableRegion: 510115000000
			},
			loading: false,
			policyList: [],
			pagination: {
				total: 0,
				pageId: 1,
				pageCount: 0,
				pageSize: 10
			}
		};
	},
	// 进入页面请求政策列表
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
		const result = await $axios.get(FETCH_POLICY_LIST, {params});
		const data = result || {};
		let industryOptions;
		let industries;

		if (sessionStorage.getItem('industries')) {
			let industryList = sessionStorage.getItem('industries');

			industries = JSON.parse(industryList);
		} else {
			industries = await $axios.get(FETCH_FIRST_INDUSTRY_LIST, params);
			sessionStorage.setItem('industries', JSON.stringify(industries));
		}
		// 请求行业数据
		// let industryOptions = await $axios.get(FETCH_FIRST_INDUSTRY_LIST, params);

		industryOptions = industries.map(item => ({
			value: item.id,
			label: item.type_name
		}));

		return {
			policyList: data.data,
			industryOptions,
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

			// 适用行业
			if (this.filters.industry && this.filters.industry.length > 0) {
				filterParams.industry = this.filters.industry.join(',');
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
			return filterParams;
		}
	},
	methods: {
		// 根据过滤参数请求政策列表
		fetchPolicyList(pageId, pageSize) {
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
			this.$axios.get(FETCH_POLICY_LIST, {params})
				.then((data = {}) => {
					this.loading = false;
					this.policyList = data.data;
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
		// 过滤参数变化
		handleFiltersChange(filters) {
			this.filters = filters;
			this.fetchPolicyList(1);
		},
		// 触发分页
		handlePageChange(pageId) {
			this.fetchPolicyList(pageId);
		}
	},
	watch: {
		// 路由变化后触发搜索
		$route() {
			this.fetchPolicyList(1);
		},
	}
};
</script>

<style lang="less" scope>
@import "~assets/css/common_avairail.less";
.policy-container {
  // padding-top: 20px;
  .result-list-container {
    margin-top: 32px;
  }
}
</style>

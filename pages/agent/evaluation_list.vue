<template>
	<div>
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
						placeholder="请输入机构名称"
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
		<div class="breadcrumb-row">
			<el-divider direction="vertical"></el-divider>
			当前位置：
			<el-breadcrumb separator-class="el-icon-arrow-right">
				<el-breadcrumb-item>
					<nuxt-link to="/">首页</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<nuxt-link to="/agent">中介服务</nuxt-link>
				</el-breadcrumb-item>
				<el-breadcrumb-item>
					<div>中介机构列表</div>
				</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<AgentFiflters
			:agentTypeList="agentTypeList"
			:filters="filters"
			:agentTotal="agentTotal"
			@onChange="handleFiltersChange"
		/>
		<p>为您找到以下中介机构信息:</p>
		<EvaluateList
			:isShowShadow="true"
			:isShowDivider="false"
			:agentList="agentList"
			v-loading="loading"
			:capthaPicture="capthaPicture"
		/>
		<Pagination
			:pagination="pagination"
			@onPageChange="onPageChange"
		/>
	</div>
</template>
<script>
import {
	AGENT_TYPE,
	AGENT_LIST,
	FETCH_CAPTCHACODE
} from '@/utils/urls.js';
import {
	districtCodeMap
} from '@/utils/district';
import AgentFiflters from '@/components/agent/agent-fiflters';
import EvaluateList from '@/components/evaluate-list';
import Pagination from '@/components/pagination';
export default {
	components: {
		AgentFiflters,
		EvaluateList,
		Pagination
	},
	data() {
		return {
			loading: false,
		};
	},
	// 初始化机构列表
	async asyncData({$axios, query}) {
		let requestPramas = {
			page: 1,
			per_page: 10,
		};

		if (query.keyword) {
			requestPramas.keyword = query.keyword;
		}

		if (query.type_id) {
			requestPramas.type_id = query.type_id;
		}
		return Promise.all([
			$axios.get(AGENT_TYPE),
			$axios.get(AGENT_LIST, {params: requestPramas}),
			$axios.get(FETCH_CAPTCHACODE),
		]).then(([agentType, agentList, capthaPicture]) => ({

			agentType: agentType || [],
			agentList: agentList.data || [],
			agentTotal: agentList.total || 0,
			pagination: {
				total: agentList.total,
				pageCount: 1,
				pageSize: 10
			},
			capthaPicture: {
				img: capthaPicture.img,
				key: capthaPicture.key
			},
			filters: {
				keyword: query.keyword,
				type_id: query.type_id ? [+query.type_id] : [],
			}

		})).catch(error => {
			console.log(error);
		});
	},
	computed: {
		// 服务类型
		agentTypeList() {
			let agentTypeList = [];

			this.agentType.forEach(item => {
				agentTypeList.push({
					value: item.id,
					label: item.name
				});
			});
			return agentTypeList;
		},

		// 后端参数处理
		filterParams() {
			let filterParams = {};

			// 适用行业
			if (this.filters.type_id && this.filters.type_id.length > 0) {
				console.log('type_id222', this.filters.type_id);
				filterParams.type_id = this.filters.type_id.join(',');
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
			this.fetchAgentList(1, 10);
		},
		// 获取机构列表
		fetchAgentList(pageId, pageSize) {
			console.log(111);
			this.loading = true;
			let params = {
				...this.filterParams,
				page: pageId || this.pagination.pageCount,
				per_page: pageSize || this.pagination.pageSize,
			};

			this.$axios.get(AGENT_LIST, {params: params}).then(res => {
				this.agentList = res.data || [],
				this.agentTotal = res.total || 0,
				this.pagination.total = res.total;
				this.loading = false;
			}).catch(error => {
				console.log(error.message);
				this.loading = false;
			});
		},
		onPageChange(val) {
			this.fetchAgentList(val);
		},
		// 搜索
		handleSearch() {
			this.$router.push({
				path: 'evaluation_list',
				query: {
					keyword: this.filters.keyword
				}
			});
		},
	},
	mounted() {
		console.log('type_id', this.$route.type_id);
	},
	watch: {
		$route() {
			this.fetchAgentList(1, 10);
		}
	}
};
</script>
<style lang="less">
@import "~assets/css/common_avairail";
 // 搜索样式
  .search-container {
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
  .breadcrumb-row {
    padding-top: 40px;
  }
</style>


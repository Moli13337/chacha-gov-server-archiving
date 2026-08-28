<template>
	<div class="declaration-container">
		<div class="content-contanier">
			<el-row>
				<el-col :span="24">
					<declararion-filters
						:filters="filters"
						:moldTypeList="moldTypeList"
						@onChange="handleFiltersChange"
					/>
					<div class="result-list-container">
						<p class="result-list-tip">为你找到以下申报信息
							<!-- <span
								class="tip"
								@click="handleToGuide"
							> 温江区申报指南
              </span> -->
						</p>
						<declararion-list
							:loading="loading"
							:list="declarationList"
							:pagination="pagination"
							@onPageChange="handlePageChange"
						/>
					</div>
					<nuxt-child/>
				</el-col>
			</el-row>
		</div>
	</div>
</template>

<script>

import DeclararionList from '@/components/declaration/declararion-list';
import DeclararionFilters from '@/components/declaration/declararion-filters';
import {
	FEATCH_DECLARATION_LIST,
	MOLD_TYPE_LIST
} from '@/utils/urls.js';
import {
	districtCodeMap
} from '@/utils/district';

export default {
	components: {
		DeclararionList,
		DeclararionFilters
	},
	data() {
		return {
			// 过滤条件
			filters: {
				// 适用地区
				suitableRegion: 510115000000,
				// 申报状态
				reportStatus: [1],
				moldType: []
			},
			// 加载
			loading: false,
			// 申报列表
			declarationList: [],
			// 政策类型
			moldTypeList: [],
			// 分页器
			pagination: {
				total: 0,
				pageCount: 0,
				pageSize: 10
			},
			formInline: {
				user: '',
			}
		};
	},
	// 获取列表数据
	async asyncData({query, $axios}) {
		let params = {
			page: 1,
			per_page: 10,
			province_code: '510000000000',
			city_code: '510100000000',
			district_code: '510115000000',
			announce_status: 1
		};

		// 搜索关键字
		if (query.keyword) {
			params.keyword = query.keyword;
		}
		const result = await $axios.get(FEATCH_DECLARATION_LIST, {params});
		const data = result || {};

		return {
			declarationList: data.data,
			pagination: {
				total: data.total,
				pageId: 1,
				pageCount: data.total_page,
				pageSize: 10
			}
		};
	},
	mounted() {
		this.$axios.get(MOLD_TYPE_LIST).then(res => {
			this.moldTypeList = res;
		}).catch(error => {
			console.log(error);
		});
	},
	computed: {
		// 过滤
		filterParams() {
			let filterParams = {};

			// 项目状态
			if (this.filters.reportStatus && this.filters.reportStatus.length > 0) {
				filterParams.announce_status = this.filters.reportStatus[0];
			}
			// 政策类型
			if (this.filters.moldType) {
				filterParams.mold_id = this.filters.moldType;
			}
			// 适用地区
			if (this.filters.suitableRegion) {
				// 510115000000
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
		// 申报指南
		handleToGuide() {
			this.$router.push({name: 'index-guide', query: {guideType: 2}});
		},
		//  请求申报列表
		featchDeclarationList(pageId, pageSize) {
			if (this.$route.query.keyword) {
				this.$bus.emit('changeSearchContent', this.$route.query.keyword);
			}
			let params = {
				...this.filterParams,
				page: pageId || this.pagination.pageId,
				per_page: pageSize || this.pagination.pageSize,
			};

			// 添加搜索关键字，keyword需要填充到地址栏，便于爬虫爬取，链接别人看到的数据相同
			if (this.$route.query && this.$route.query.keyword) {
				params.keyword = this.$route.query.keyword;
			}

			this.loading = true;
			this.$axios.get(FEATCH_DECLARATION_LIST, {params})
				.then((data = {}) => {
					this.loading = false;
					this.declarationList = data.data;
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
		// 过滤参数变化,过滤器事件
		handleFiltersChange(filters) {
			this.filters = filters;
			this.featchDeclarationList(1);
		},
		// 触发分页，请求列表
		handlePageChange(pageId) {
			this.featchDeclarationList(pageId);
		}
	},
	watch: {
		// 路由变化后触发搜索
		$route() {
			this.featchDeclarationList(1);
		},
	}
};
</script>

<style lang="less">
@import "~assets/css/common_avairail.less";
.declaration-container {
  .content-contanier {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
    .el-row {
      width: 100%;
    }
    .result-list-container {
      margin-top: 32px;
    }
    .side-bar {
      .side-image {
        width: 100%;
        height: 250px;
        .small {
          width: 100%;
        }
      }
      .side-tip-container {
        .side-tip-item {
          height: 102px;
          padding: 27px;
          margin-top: 20px;
          display: flex;
          justify-content: space-between;
          box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.05);
          background: @backGroundColor;
          .tip-title {
            font-size:18px;
            font-family:Microsoft YaHei;
            font-weight:bold;
          }
          .tip-describe {
            font-size:14px;
            font-family:Microsoft YaHei;
            font-weight:400;
            color: @textColor;
            padding: 10px 0;
          }
          .icon {
            font-size: 50px;
            color: @primaryColor;
          }
        }
      }
    }
    .filters-option {
      width: 100%;
      height: 78px;
       padding: 16px;
      box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.05);
      background: @backGroundColor;
      display: flex;
      justify-content: space-around;
      .el-input__inner {
        width: 232px;
        border-radius: 0;
      }
    }
  }
  .tip {
    color: @poliyItemColor;
  }
  .tip:hover {
    color: @primaryColor;
  }
}
</style>

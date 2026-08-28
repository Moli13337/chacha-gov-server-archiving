<template>
	<div class="policy-list-container">
		<Empty
			v-if="list && list.length == 0"
			tip="暂无政策信息"
		/>
		<div v-else>
			<ul
				class="policy-list"
				v-loading="loading"
			>
				<li
					class="policy-item"
					v-for="(item, index) in list"
					:key="index"
					@click="handleItemClick(item.id)"
				>
					<p class="title">{{item.name}}</p>
					<div class="metas">
						<p
							class="meta"
						>
							<span class="meta-title"><i class="iconfont icon-dizhi"></i></span>
							<span
								class="meta-content"
								v-if="!item.province_name && !item.city_name && !item.district_name"
							>全国</span>
							<span
								class="meta-content"
								v-else
							>{{item.province_name}}{{item.city_name}}{{item.district_name}}</span>
						</p>
						<p
							class="meta"
							v-if="item.industry && item.industry.length > 0"
						>
							<span class="meta-title"><i class="iconfont icon-xingye"></i></span>
							<span
								class="meta-content"
								v-for="(item, index) in item.industry"
								:key="index"
							>{{item}}</span>
						</p>
						<p
							class="meta"
							v-if="item.validity_sdate || item.validity_edate"
						>
							<span class="meta-title">有效期:</span>
							<span class="meta-content">{{item.validity_sdate | formatDate}} - {{item.validity_edate | formatDate}}</span>
						</p>
					</div>
				</li>
			</ul>
			<div
				class="policy-pagination"
				v-if="pagination.pageCount > 1"
			>
				<el-pagination
					background
					prev-text='上一页'
					next-text='下一页'
					layout="prev, pager, next"
					:total="pagination.total"
					:page-count="pagination.pageCount"
					:page-size="pagination.pageSize"
					@current-change="$emit('onPageChange', $event)"
				/>
			</div>
		</div>
	</div>
</template>

<script>
import Empty from '@/components/empty';
export default {
	components: {
		Empty
	},
	props: {
		loading: {
			type: Boolean,
			default: false,
		},
		list: {
			type: Array,
			default() {
				return [];
			}
		},
		pagination: {
			type: Object,
			default() {
				return {};
			}
		}
	},
	methods: {
		handleItemClick(id) {
			let routeData = this.$router.resolve({
				name: 'policy-detail',
				query: {
					id
				}
			});

			window.open(routeData.href, '_blank');
		}
	},
};
</script>

<style lang="less" scope>
@import "~assets/css/common_avairail.less";
.policy-list-container {
  .policy-list {
    .policy-item {
      margin: 24px 0;
      padding: 16px 30px;
      box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.05);
      background: @backGroundColor;
      border: 1px solid @backGroundColor;
      cursor: pointer;
      .title {
        font-size: 16px;
        font-family: Microsoft YaHei;
        font-weight: bold;
        padding: 8px 0;
      }
      .description {
        font-size: 14px;
        font-family: Microsoft YaHei;
        font-weight: 400;
        color: @textColor;
        margin: 10px 0;
        min-height: 50px;
        -webkit-line-clamp: 2;
        // display: -webkit-box;
        -webkit-box-orient:vertical;
        overflow:hidden;
        text-overflow: ellipsis;
        table {
        border-collapse: collapse;
        tr,th,td {
          border: 1px solid @borderLine;
        }
        }
      }
      .metas {
        display: flex;
        flex-wrap: wrap;
        padding: 4px 0;
        margin-top: 8px;
        .meta {
          font-size: 14px;
          font-family: Microsoft YaHei;
          font-weight: 400;
          margin-right: 40px;
          .meta-title {
            color: #cbcbcb;
          }
          .meta-content {
            margin-left: 4px;
            color: @textColor;
          }
        }
      }
    }
    .policy-item:hover {
      box-shadow: none;
      .title {
        color: @primaryColor;
      }
    }
  }
  .policy-pagination {
    margin: 40px 0;
    display: flex;
    justify-content: center;
    .number {
      width: 42px;
      height: 41px;
      line-height: 41px;
      background: @backGroundColor;
    }
    .el-icon-more {
      background: none;
      height: 41px;
      line-height: 41px;
    }
    .btn-prev, .btn-next {
      height: 41px;
      width: 92px;
      font-size: 20px;
      background: @backGroundColor;
    }
  }
}
</style>

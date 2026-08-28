<template>
	<div class="list-container">
		<Empty
			v-if="list && list.length == 0"
			tip="暂无公示公告信息"
		/>
		<div v-else>
			<ul
				class="
			list"
				v-loading="loading"
			>
				<li
					class="item"
					v-for="(item, index) in list"
					:key="index"
					@click="handleDetail(item.id,item.obj_type)"
				>
					<p class="title"><span>【{{item.obj_type_name}}通知】{{item.name}}</span>
						<span
							class="tip"
							v-if="item.expired == 0 && item.is_new == 1"
						>
							new
						</span>
					</p>
					<div
						class="description"
						v-html="richTextToEllipsis(item.content, 150)"
					></div>
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
							v-if="item.pub_time"
						>
							<span class="meta-title">{{item.obj_type_name == '申报' ? '上传时间': '发布时间'}} </span>
							<span class="meta-content">{{item.pub_time | formatDate}}</span>
						</p>
						<p
							class="meta"
							v-if="item.validity_sdate && item.validity_edate"
						>
							<span class="meta-title">{{item.obj_type_name == '申报' ? '集中申报时间': item.obj_type_name == '活动'?'公示时间':''}}</span>
							<span class="meta-content">{{item.validity_sdate | formatDate}} - {{item.validity_edate | formatDate}} </span>
						</p>
					</div>
				</li>
			</ul>
			<div
				class="pagination"
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
		handleDetail(id, obj_type) {
			switch (obj_type) {
				case 7: {
					let routeData =	this.$router.resolve({name: 'notice-activity', query: {id: id}});

					window.open(routeData.href, '_blank');
					break;
				}
				case 10: {
					let routeData = this.$router.resolve({name: 'notice-appropriation', query: {id: id}});

					window.open(routeData.href, '_blank');
					break;
				}
				default: {
					let routeData = this.$router.resolve({name: 'notice-declare', query: {id: id}});

					window.open(routeData.href, '_blank');
				}
			}
		},
	},
};
</script>

<style lang="less" scope>
@import "~assets/css/common_avairail.less";
.list-container {
  .list {
    .item {
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
        display: flex;
        justify-content: space-between;
        .tip {
          width: 100px;
          font-size:14px;
          font-family:Microsoft YaHei;
          font-weight:400;
          color:#FF4646;
          text-align: center;
        }
      }
      .description {
        font-size: 14px;
        font-family: Microsoft YaHei;
        font-weight: 400;
        color: @textColor;
        margin: 10px 0;
        min-height: 40px;
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
    .item:hover {
      box-shadow: none;
      .title {
        color: @primaryColor;
      }
    }
  }
  .pagination {
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

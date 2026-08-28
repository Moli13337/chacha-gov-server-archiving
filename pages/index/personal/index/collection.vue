<template>
	<el-col :span="19">
		<div class="personal-content">
			<div class="top">
				<p>
					<el-divider direction="vertical"></el-divider>
					<span>我的收藏</span>
				</p>
				<div class="message-type">
					<p class="tip">内容类型</p>
					<el-select
						v-model="obj_type"
						placeholder="请选择消息类型"
						@change="handleFilterChange"
					>
						<el-option
							:label="item.name"
							:value="item.id"
							v-for="(item, index) in collectionTypeOptions"
							:key="index"
						></el-option>
					</el-select>
				</div>
			</div>
			<!-- 消息为空 -->
			<Empty v-if="!collectionList || !collectionList.length"/>
			<div class="list-container">
				<ul
					v-loading="loading"
					class="list"
				>
					<li
						class="item"
						v-for="(item, index) in collectionList"
						:key="index"
						@click="handleDetail(item.id,item.obj_type)"
					>
						<!-- 通知类型 -->
						<div v-if="item.obj_type == 4 || item.obj_type == 7 || item.obj_type == 10">
							<p class="title">
								<span>【{{item.obj_type_name}}通知】{{item.name}}</span>
								<span
									class="tip"
									v-if="item.expired == 0 && item.is_new == 1"
								>new</span>
							</p>
							<div
								class="description"
								v-html="richTextToEllipsis(item.content, 150)"
							></div>
							<div class="metas">
								<p class="meta">
									<span class="meta-title">
										<i class="iconfont icon-dizhi"></i>
									</span>
									<span
										class="meta-content"
										v-if="!item.province_name && !item.city_name && !item.district_name"
									>
										<i class="el-icon-map-location location-icon"></i>全国
									</span>
									<span
										class="meta-content"
										v-else
									>
										<i class="el-icon-map-location location-icon"></i>
										{{item.province_name}}{{item.city_name}}{{item.district_name}}
									</span>
								</p>
								<p
									class="meta"
									v-if="item.pub_time"
								>
									<span class="meta-title">{{item.obj_type_name == '申报' ? '上传时间': '发布时间'}}</span>
									<span class="meta-content">{{item.pub_time | formatDate}}</span>
								</p>
								<p
									class="meta"
									v-if="item.validity_sdate && item.validity_edate"
								>
									<span
										class="meta-title"
									>{{item.obj_type_name == '申报' ? '集中申报时间': item.obj_type_name == '活动'?'公示时间':''}}</span>
									<span
										class="meta-content"
									>{{item.validity_sdate | formatDate}} - {{item.validity_edate | formatDate}}</span>
								</p>
							</div>
						</div>
						<!-- 政策类型 -->
						<div v-if="item.obj_type == 1">
							<p class="title">{{item.name}}</p>
							<div class="metas">
								<p class="meta">
									<span class="meta-title">
										<i class="iconfont icon-dizhi"></i>
									</span>
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
									<span class="meta-title">
										<i class="iconfont icon-xingye"></i>
									</span>
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
									<span
										class="meta-content"
									>{{item.validity_sdate | formatDate}} - {{item.validity_edate | formatDate}}</span>
								</p>
							</div>
						</div>
						<!-- 申报项目 -->
						<div
							v-if="item.obj_type == 8"
							class="dealare-type-box"
						>
							<div class="title">
								<span class="title-text">{{item.name}}</span>
								<p
									class="item-tip"
									:class="item.announce_status == 1 ? 'tip' : item.announce_status == 2 ? 'tip-ready ' : 'tip-complate' "
								>{{item.announce_status_desc}}</p>
							</div>
							<div
								class="description"
								v-html="richTextToEllipsis(item.sup_content, 150)"
							></div>
							<div class="metas">
								<p class="meta">
									<span class="meta-title">适用区域:</span>
									<span
										class="meta-content"
										v-if="!item.province_name && !item.city_name && !item.district_name"
									>全国</span>
									<span
										class="meta-content"
									>{{item.province_name}}{{item.city_name}}{{item.district_name}}</span>
								</p>
								<p
									class="meta"
									v-if="item.created_at"
								>
									<span class="meta-title">上传时间:</span>
									<span class="meta-content">{{item.created_at | formatDate}}</span>
								</p>
								<p class="meta">
									<span class="meta-title">集中申报时间:</span>
									<span
										class="meta-content"
									>{{item.validity_sdate | formatDate}} - {{item.validity_edate | formatDate}}</span>
								</p>
							</div>
						</div>
						<!-- 中介机构 -->
						<div
							class="orange-item"
							v-if="item.obj_type == 16"
						>
							<div class="image-box">
								<img
									class="image"
									:src="item.file_url"
								/>
							</div>
							<div class="text-box">
								<div class="name">
									<p>
										<span class="organ-name">{{item.agent_name}}</span>
										<span class="type">{{item.agent_type_name}}</span>
									</p>
								</div>
								<p class="line">
									<span class="content-title">服务事项：</span>
									{{item.service_item}}
								</p>
								<p class="line">
									<span class="content-title">机构地址：</span>
									{{item.province_name == item.city_name ? '' : item.province_name}}{{item.city_name}}{{item.district_name}}{{item.address}}
								</p>
								<div class="bottom-tip line">
									<p>
										<span class="tip-title">联系人：</span>
										<span>{{item.contact_name}}</span>
									</p>
									<p>
										<span class="tip-title">联系电话：</span>
										<span>{{item.contact_phone}}</span>
									</p>
									<p>
										<span>综合评价：</span>
										<el-rate
											style="display: inline-block"
											v-model="item.composite_stars"
											disabled
											text-color="#ff9900"
											score-template="{value}"
										></el-rate>
									</p>
									<p>
										<span>部门评价：</span>
										<el-rate
											style="display: inline-block"
											v-model="item.department_stars"
											disabled
											text-color="#ff9900"
											score-template="{value}"
										></el-rate>
									</p>
								</div>
							</div>
						</div>
					</li>
				</ul>
				<div class="pagination">
					<el-pagination
						background
						prev-text="上一页"
						next-text="下一页"
						layout="prev, pager, next"
						:total="pagination.total"
						:page-count="pagination.pageCount"
						:page-size="pagination.pageSize"
						@current-change="handlePageChange"
					/>
				</div>
			</div>
			<el-dialog
				:visible.sync="detailDialogVisible"
				width="45%"
				center
				class="dialog"
			>
				<template slot="title">
					<p
						class="title"
						style="background='red'"
					>消息通知详情</p>
				</template>
				<template>
					<div class="tip">
						<p>尊敬的用户:</p>
						<p>{{currMessage && currMessage.title}}</p>
					</div>
				</template>
				<div
					slot="footer"
					class="dialog-footer"
				>
					<p>具体内容:</p>
					<div class="content">{{currMessage && currMessage.content}}</div>
				</div>
			</el-dialog>
		</div>
	</el-col>
</template>
<script>
import {
	FETCH_COLLECTION_LIST
} from '@/utils/urls.js';
import Empty from '@/components/empty';

export default {
	components: {
		Empty
	},
	props: {
		value: {
			type: Number,
			default: 0
		}
	},
	data() {
		return {
			loading: false,
			pagination: {
				total: 0,
				pageId: 1,
				pageCount: 0,
				pageSize: 10
			},
			detailDialogVisible: false,
			currMessage: null,
			obj_type: '',
			collectionTypeOptions: [
				{
					id: '',
					name: '全部'
				},
				{
					id: 1,
					name: '宏观政策'
				},
				{
					id: 4,
					name: '申报公示公告'
				},
				{
					id: 7,
					name: '活动公示公告'
				},
				{
					id: 8,
					name: '项目'
				},
				{
					id: 10,
					name: '拨款公示公告'
				},
				{
					id: 16,
					name: '中介机构'
				}
			]
		};
	},

	async asyncData({$axios}) {
		let params = {
			page: 1,
			per_page: 10
		};

		return Promise.all([$axios.get(FETCH_COLLECTION_LIST, {params})])
			.then(([result]) => {
				const data = result || {};

				return {
					collectionList: data.data || [],
					pagination: {
						total: data.total,
						pageId: 1,
						pageCount: data.total_page,
						pageSize: 10
					}
				};
			})
			.catch(e => {
				console.log(e);
			});
	},
	methods: {
		// 获取消息列表数据
		fetchCollectionList(pageId, pageSize) {
			let params = {
				page: pageId || this.pagination.pageId,
				per_page: pageSize || this.pagination.pageSize,
				obj_type: this.obj_type
			};

			this.loading = true;
			this.$axios
				.get(FETCH_COLLECTION_LIST, {params: params})
				.then((data = {}) => {
					this.loading = false;
					this.collectionList = data.data;
					this.pagination = {
						...this.pagination,
						total: data.total,
						pageId: data.current_page,
						pageCount: data.total_page
					};
				})
				.catch(() => {
					this.loading = false;
					this.$message.error('获取收藏列表失败，请重试');
				});
		},

		// 页码改变
		handlePageChange(pageId) {
			this.fetchCollectionList(pageId);
		},
		// 过滤条件改变
		handleFilterChange() {
			this.fetchCollectionList(1);
		},
		policyDetail(id) {
			let routeData = this.$router.resolve({
				path: '/policy/detail',
				query: {
					id: id
				}
			});

			window.open(routeData.href, '_blank');
		},
		declareNoticeDetail(id) {
			let routeData = this.$router.resolve({
				path: '/notice/declare',
				query: {
					id: id
				}
			});

			window.open(routeData.href, '_blank');
		},
		activityDetail(id) {
			let routeData = this.$router.resolve({
				path: '/notice/activity',
				query: {
					id: id
				}
			});

			window.open(routeData.href, '_blank');
		},
		appropriationDetail(id) {
			let routeData = this.$router.resolve({
				path: '/notice/appropriation',
				query: {
					id: id
				}
			});

			window.open(routeData.href, '_blank');
		},
		agentDetail(id) {
			let routeData = this.$router.resolve({
				path: '/agent/organ_detail',
				query: {
					id: id
				}
			});

			window.open(routeData.href, '_blank');
		},
		declareDetail(id) {
			let routeData = this.$router.resolve({
				path: '/declaration/detail',
				query: {
					id: id
				}
			});

			window.open(routeData.href, '_blank');
		},
		// 跳转详情
		handleDetail(id, type) {
			switch (type) {
				case 1:
					this.policyDetail(id);
					break;
				case 4:
					this.declareNoticeDetail(id);
					break;
				case 7:
					this.activityDetail(id);
					break;
				case 10:
					this.appropriationDetail(id);
					break;
				case 8:
					this.declareDetail(id);
					break;
				case 16:
					this.agentDetail(id);
					break;
				default:
					break;
			}
		}
	}
};
</script>

<style lang="less">
@import "~assets/css/common_avairail.less";
.personal-content {
  background: #ffffff;
  border: 1px solid rgba(235, 235, 235, 1);
  .el-divider--vertical {
    width: 11px;
    height: 34px;
    background: @primaryColor;
  }
  .top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid @defaultBorderColor;
    .message-type {
      margin-right: 30px;
      display: flex;
      justify-content: space-around;
      align-items: center;
      .tip {
        padding: 0 10px;
        font-size: 16px;
        font-family: Microsoft YaHei;
        font-weight: 400;
        color: @borderLine;
      }
    }
  }
  .list-container {
    padding: 20px 30px 30px 30px;
    .list {
      .item {
        margin: 24px 0;
        padding: 16px 30px;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.05);
        background: @backGroundColor;
        border: 1px solid #dcdfe6;
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
            font-size: 14px;
            font-family: Microsoft YaHei;
            font-weight: 400;
            color: #ff4646;
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
          -webkit-box-orient: vertical;
          overflow: hidden;
          text-overflow: ellipsis;
          table {
            border-collapse: collapse;
            tr,
            th,
            td {
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
  }
  .dialog {
    .el-dialog__header {
      background: @primaryColor;
      color: #ffffff;
      font-size: 18px;
      text-align: left;
      font-weight: bold;
    }
    .el-icon-close {
      color: #ffffff;
      font-size: 20px;
    }
    .tip {
      color: @defaultTextColor;
      font-weight: bold;
      padding: 20px 20px 0 20px;
    }
  }
  .dialog-footer {
    text-align: left;
    padding: 0px 20px 0 20px;
    .content {
      border: 1px solid @defaultBorderColor;
      padding: 10px;
      margin-top: 10px;
    }
  }
  .orange-item {
    background: #ffffff;
    display: flex;
    justify-content: flex-start;
    margin-top: 20px;
    .evaluate-list-container-list {
      padding: 20px;
      box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.05);
    }
    .image-box {
      width: 141px;
      height: 100%;
      margin-right: 10px;
    }
    .text-box {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .image {
      width: 141px;
      height: 141px;
    }
    .name {
      font-weight: bold;
      margin-right: 10px;
      display: flex;
      justify-content: space-between;
    }
    .type {
      display: inline-block;
      width: 63px;
      height: 23px;
      font-size: 12px;
      background: rgba(3, 109, 180, 1);
      color: #ffffff;
      text-align: center;
      line-height: 23px;
      border-radius: 4px;
    }
    .content {
      font-size: 14px;
      color: rgba(129, 129, 129, 1);
      .content-title {
        color: #3b3b3b;
        font-weight: Bold;
      }
    }
    .bottom-tip {
      display: flex;
      justify-content: space-between;
      color: rgba(129, 129, 129, 1);
      font-size: 12px;
    }
    .tip-title {
      color: #3b3b3b;
    }
    .icon-edit {
      width: 15px;
      height: 15px;
      margin-right: 5px;
    }
    .organ-name:hover {
      color: @primaryColor;
    }
  }
  .dealare-type-box {
    .item-tip {
      display: inline-block;
      height: 25px;
      line-height: 25px;
      padding: 0 20px;
      font-weight: 400;
      font-family: Microsoft YaHei;
      font-size: 14px;
      color: @backGroundColor;
    }
    .tip {
      color: #ffffff !important;
      background: rgba(204, 0, 0, 1);
    }
    .tip-ready {
      background: #3b3b3b;
    }
    .tip-complate {
      background: #818181;
    }
  }
}
</style>


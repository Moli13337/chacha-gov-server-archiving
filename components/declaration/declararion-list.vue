<template>
	<div class="declararion-list-container">
		<Empty
			v-if="list && list.length == 0"
			tip="暂无申报信息"
		/>
		<div v-else>
			<ul
				class="list"
				v-loading="loading"
			>
				<li
					class="item"
					v-for="(item, index) in list"
					:key="index"
				>
					<div
						class="title"
						@click="handleDetailClick(item.id)"
					><span class="title-text">{{item.name}}</span>
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
							<span class="meta-content">{{item.province_name}}{{item.city_name}}{{item.district_name}}</span>
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
							<span class="meta-content">{{item.validity_sdate | formatDate}} - {{item.validity_edate | formatDate}}</span>
						</p>
					</div>
					<div
						class="declaration-online"
					>
						<el-divider></el-divider>
						<div class="bottom-content">
							<div>
								<el-button
									type="primary"
									class="declaration-btn"
									@click="handleDeclareClick(item)"
									:centerDialogVisible = "centerDialogVisible"
								>
									在线申报
								</el-button>
							</div>
							<div class="tip-declaration">
								<p>点击按钮即刻开始</p>
								<p>政策快速申报</p>
							</div>
						</div>
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
		<!-- 对话框 -->
		<el-dialog
			:visible.sync="centerDialogVisible"
			width="400px"
			center
		>
			<p style="padding: 0px 60px 20px 40px;">申报{{currentItem && currentItem.expired_desc}}，仍可提交申报列表 <br>是否继续？</p>
			<div
				class="dialog-btns"
				style="padding: 0px 60px 0px 40px;"
			>
				<el-button @click="closeConfirmDialog">取 消</el-button>
				<el-button
					type="primary"
					@click="handleContinueClick"
					style="margin-left: 60px;"
				>继续</el-button>
			</div>
			<p
				slot="footer"
				class="dialog-footer"
				style="padding: 0px 40px; color: #CBCBCB; text-align: left;"
			>如有疑问，请联系成都侠客岛企业管理有限公司,联系电话400-900-9088</p>
		</el-dialog>
	</div>
</template>

<script>
import storage from '@/utils/storage';
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
	data() {
		return {
			currentItem: null,
			status: '',
			centerDialogVisible: false,
		};
	},
	computed: {
		statusText() {
			if (this.currentItem) {
				switch (this.currentItem.status) {
					case 2:
						return '申报即将开始';
					case 3:
						return '申报即将结束';
					default:
						return '';
				}
			}
			return '';
		},
	},
	methods: {

		// 在线申报
		goToDeclare(id) {
			let routeData = this.$router.resolve({
				name: 'declaration-online-mode',
				params: {mode: 'create'},
				query: {id}
			});

			window.open(routeData.href, '_blank');
		},
		showConfirmDialog(item) {
			this.currentItem = item;
			this.centerDialogVisible = true;
		},
		closeConfirmDialog() {
			this.currentItem = null;
			this.centerDialogVisible = false;
		},

		// 查看申报详情
		handleDetailClick(id) {
			let routeData = this.$router.resolve({
				name: 'declaration-detail',
				query: {id}
			});

			window.open(routeData.href, '_blank');
		},
		handleDeclareClick(item) {
			let userInfo = storage.getItem('user_info');
			let _token = storage.getItem('token');

			// 判断是否登录
			if (!_token) {
				this.$message.error('请先登录');
				// this.$router.push('/login');
				this.tencentLogin();
				return;
			}

			// 判断用户是否注册企业信息，若无，则重定向为企业认证
			if (!userInfo || !userInfo.enterprise || !userInfo.enterprise.length) {
				this.$message.error('请先进行企业认证');
				console.log('请先进行企业认证');
				this.$router.push({name: 'certification'});
				return;
			}

			// 不需要弹窗提示
			if (item.announce_status == 1) {
				this.goToDeclare(item.id);
			} else {
				this.showConfirmDialog(item);
			}
		},
		handleCloseClick() {
			this.currentItem = null;
			this.centerDialogVisible = false;
		},
		handleContinueClick() {
			this.goToDeclare(this.currentItem.id);
			this.closeConfirmDialog();
		}
	},
};
</script>

<style lang="less">
@import "~assets/css/common_avairail.less";
.declararion-list-container {
  .list {
    .item {
      margin: 24px 0;
      padding: 16px 30px;
      box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.05);
      background: @backGroundColor;
      border: 1px solid #ffffff;
      .title {
        font-size: 16px;
        font-family: Microsoft YaHei;
        font-weight: bold;
        padding: 8px 0;
        .title-text {
          max-width: 1000px;
        }
        .item-tip {
          display: inline-block;
          // width:87px;
          height:25px;
          line-height: 25px;
          padding: 0 20px;
          font-weight:400;
          font-family:Microsoft YaHei;
          font-size:14px;
          color: @backGroundColor;
        }
        .tip {
          background:rgba(204,0,0,1);
        }
        .tip-ready {
          background:#3B3B3B;
        }
        .tip-complate {
          background:#818181;
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
      cursor: pointer;
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
  .declaration-online {
    .declaration-btn {
      width: 183px;
      height:49px;
      border-radius:5px;
    }
    .bottom-content {
      display: flex;
    }
    .tip-declaration {
      // display: inline-block;
      font-size:12px;
      font-family:Microsoft YaHei;
      font-weight:400;
      color: #CBCBCB;
      margin-left: 10px;
      height: 49px;
      vertical-align: bottom;
      display: flex;
      flex-direction: column;
      justify-content: center;

    }
  }
  .dialog {
    .dialog-btns {
      height: 50px;
      .el-dialog__body {

      }
    }
    .dialog-footer .el-dialog__footer{
      font-size: 10px;
      color: @textColor;
    }
  }
}
</style>

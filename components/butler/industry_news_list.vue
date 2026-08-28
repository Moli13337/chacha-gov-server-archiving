<template>
	<div class="project_recommentd_list-container">
		<Empty
			v-if="list && list.length == 0"
			tip="暂无行业动态信息"
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
					><span class="title-text">【行业动态】{{item.title}}</span>
					</div>
					<div
						class="description"
						v-html="richTextToEllipsis(item.content, 150)"
					></div>
					<div class="metas">
						<p class="meta">
							<span class="meta-title">来源：</span>
							<span class="meta-content">{{item.source_name || '--'}}</span>
						</p>
						<p
							class="meta"
							v-if="item.publish_time"
						>
							<span class="meta-title">发布时间：</span>
							<span class="meta-content">{{item.publish_time | formatDate}}</span>
						</p>
					</div>
				</li>
			</ul>
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
		// 查看申报详情
		handleDetailClick(id) {
			let routeData = this.$router.resolve({
				path: '/butler/butler_components/industry_news',
				query: {
					id,
					type: 'information'
				}
			});

			window.open(routeData.href, '_blank');
		},

	},
};
</script>

<style lang="less">
@import "~assets/css/common_avairail.less";
.project_recommentd_list-container {
  .list {
    .item {
      margin: 24px 0;
      padding: 16px 30px;
      background: @backGroundColor;
      border: 1px solid @borderLine;
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
    }
    .dialog-footer .el-dialog__footer{
      font-size: 10px;
      color: @textColor;
    }
  }
}
</style>
